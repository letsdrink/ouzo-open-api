# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Environment

PHP and Composer are **not installed on the host**. All commands must run inside a Docker container with PHP 8.2+. The repo contains no Dockerfile or compose file, so use ad-hoc `docker run`. Confirm with the user if they prefer a specific image/tag before committing scripts.

Suggested invocations from the repo root:

- Install deps: `docker run --rm -v "$PWD":/app -w /app composer:latest install`
- Run all tests: `docker run --rm -v "$PWD":/app -w /app php:8.2-cli vendor/bin/phpunit tests`
- Run a single test class: `docker run --rm -v "$PWD":/app -w /app php:8.2-cli vendor/bin/phpunit tests/OpenApi/OpenApiServiceTest.php`
- Run a single test method: `docker run --rm -v "$PWD":/app -w /app php:8.2-cli vendor/bin/phpunit --filter shouldGenerateJsonWithOpenApi tests`

PHPUnit is configured (`phpunit.xml`) with `failOnDeprecation="true"` and `failOnNotice="true"` — any deprecation/notice fails the suite.

PHP requirement: `>=8.2`. Tests in `tests/` are also autoloaded under the `Ouzo\` PSR-4 prefix (see `composer.json` `autoload-dev`), so test classes live alongside source in the same namespace tree.

## Architecture

This library generates OpenAPI 3.0.1 specs from an Ouzo framework application's routing table. The big picture, in execution order:

1. **`OpenApiService::create()`** is the entry point. It is wired via Ouzo's DI (`#[Inject]` constructors) and orchestrates three collaborators: `PathsService`, `ComponentsService`, and `OpenApiCustomizersRepository`.
2. **`PathsService`** pulls `RouteRule[]` from a user-supplied **`RouteRulesProvider`** (the one interface consumers must implement), filters out `#[Hidden]` controllers/methods via `HiddenChecker`, and turns each rule into a `PathItem`/`Operation` keyed by URI + HTTP method.
3. **`OperationService`** uses reflection on the controller class/method to build `parameters`, `requestBody`, and `responses` — delegating to `ParametersService`, `RequestBodyService`, and `ContentService`. Every type encountered that produces a schema (request/response bodies, nested objects) is registered into `SchemasRepository` as a side effect.
4. **`ComponentsService`** then drains `SchemasRepository` into the OpenAPI `components.schemas` block.
5. **`OpenApiCustomizer`** implementations registered in `OpenApiCustomizersRepository` get the final `OpenApi` object to mutate (typically to set `info` and `servers`, which the core pipeline does not populate).

`SchemasRepository` is the central piece for schema generation and worth understanding before changing type/schema code:
- It is a singleton (see `DefaultOpenApiModule`) so all services share one schema registry.
- `add(Type $type)` recurses through class properties, collecting nested schemas. Already-registered short class names are skipped to avoid cycles.
- Polymorphism is driven by Symfony's `#[DiscriminatorMap]`: parent class becomes a discriminator schema; mapping classes become `allOf` children referencing the parent. `getSchemaForDiscriminator` rewrites property schemas into `oneOf` (or array-of-`oneOf`) when a property's type is a known discriminator parent.
- Backed enums are emitted as `EnumSchema` using the enum's backing type.
- The `#[Schema]` property attribute (`src/OpenApi/Attributes/Schema.php`) marks `required`/`nullable`.

Type resolution lives in `src/OpenApi/Util/Type/`:
- `TypeUtils::getForProperty` builds a `Type` from PHP reflection plus PHPDoc (parsed via `phpdocumentor/reflection-docblock`). PHPDoc is the source of truth for `T[]` array element types — see the `arrayInReturnWithoutTypeInPhpDoc` fixture for the no-PHPDoc fallback behavior.
- `CompoundType` / `ScalarType` / `DocCommentType` enumerate the type categories the rest of the code switches over.

### Extension points for consumers

- `RouteRulesProvider` (required) — supplies the routes to document.
- `OpenApiCustomizer` (optional, multiple) — populate `info`, `servers`, or post-process the spec.
- `#[Hidden]` on controller class or method — exclude from the generated spec.
- `#[Schema(required:, nullable:)]` on DTO properties.

### Testing approach

`OpenApiServiceTest` is a golden-file test: it wires the full graph manually (no DI container), serializes the resulting `OpenApi` via Symfony Serializer, and compares against `tests/OpenApi/expected-openapi.json`. When schema/path output legitimately changes, update that JSON file. Fixtures under `tests/Fixtures/` (especially `SampleController` and `SampleClass`) are the canonical examples of every supported PHP type → OpenAPI shape.
