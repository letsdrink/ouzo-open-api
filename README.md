# Ouzo OpenAPI

Generate an [OpenAPI 3.0.1](https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md) specification
directly from an [Ouzo framework](https://github.com/letsdrink/ouzo) application's routing table — no hand-written
annotations required.

## What it does

Given a list of Ouzo `RouteRule`s, the library reflects on each controller method and produces a fully populated
`OpenApi` document:

- **Paths & operations** are derived from your routes (HTTP method + URI + controller@action).
- **Path & query parameters** are read from method arguments (`:id` segments become `path` parameters; remaining scalars
  become `query` parameters).
- **Request bodies** are read from object-typed method arguments.
- **Responses** are read from method return types — including `void`, scalars, nullables, arrays of scalars/objects,
  and PHPDoc-typed arrays (`@return Tag[]`).
- **Component schemas** are collected automatically by walking class properties recursively, with cycle protection.
- **Polymorphism** via Symfony's `#[DiscriminatorMap]` is rendered as `oneOf` + discriminator mapping, with concrete
  classes emitted as `allOf` children.
- **Backed enums** (`int` / `string`) are emitted as enum schemas with the correct backing type.

The result is a plain object graph you can serialize to JSON or YAML with Symfony Serializer.

## Requirements

- PHP **>= 8.4**
- `letsdrink/ouzo`
- `symfony/serializer`
- `phpdocumentor/reflection-docblock` (used to read PHPDoc array element types)

## Installation

```bash
composer require letsdrink/ouzo-open-api
```

## Usage

### 1. Provide your routes

Implement [`RouteRulesProvider`](src/OpenApi/RouteRulesProvider.php) so the library knows which routes to document.
Typically you return whatever Ouzo has already registered in its router.

```php
use Ouzo\OpenApi\RouteRulesProvider;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;

class AppRouteRulesProvider implements RouteRulesProvider
{
    /** @return RouteRule[] */
    public function get(): array
    {
        return Route::getRoutes();
    }
}
```

### 2. (Optional) Customize the document

The generator does not populate `info` or `servers` — supply them via an
[`OpenApiCustomizer`](src/OpenApi/Customizer/OpenApiCustomizer.php). Multiple customizers may be registered; each
receives the final `OpenApi` instance and can mutate it.

```php
use Ouzo\OpenApi\Customizer\OpenApiCustomizer;
use Ouzo\OpenApi\Model\Info\Info;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\Servers\Server;

class AppInfoCustomizer implements OpenApiCustomizer
{
    public function customize(OpenApi $openApi): void
    {
        $openApi
            ->setInfo(new Info()
                ->setTitle('My API')
                ->setDescription('Public API')
                ->setVersion('1.0.0'))
            ->setServers([new Server()->setUrl('https://api.example.com')]);
    }
}
```

### 3. Generate the spec

`OpenApiService` is wired through Ouzo's DI container (see `DefaultOpenApiModule`):

```php
$openApi = $injector->getInstance(OpenApiService::class)->create();

// Serialize with symfony/serializer to JSON or YAML
echo $serializer->serialize($openApi, 'json');
```

## Annotating your code

The library favors convention — most types resolve from PHP reflection and PHPDoc alone — but two attributes give you
fine-grained control.

### `#[Hidden]` — exclude routes from the spec

Apply on a controller class (hides every action) or on a single method.

```php
use Ouzo\OpenApi\Attributes\Hidden;

class InternalController
{
    #[Hidden]
    public function debugDump(): void {}
}
```

### `#[Schema]` — mark properties as required / nullable

```php
use Ouzo\OpenApi\Attributes\Schema;

class CreateUser
{
    #[Schema(required: true)]
    public string $email;

    #[Schema(nullable: true)]
    public ?string $nickname;
}
```

### Arrays in PHPDoc

Element types for `array` are read from PHPDoc. Without it the property/return is emitted as a generic array.

```php
/** @return Tag[] */
public function listTags(): array { /* ... */ }
```

### Polymorphism

Use Symfony's `#[DiscriminatorMap]` on the parent type and the library will emit a discriminator schema plus `oneOf`
references where the parent type is used.

## Architecture (brief)

`OpenApiService::create()` orchestrates three collaborators:

1. `PathsService` — turns each `RouteRule` into a `PathItem`/`Operation` (skipping `#[Hidden]` ones via
   `HiddenChecker`).
2. `ComponentsService` — drains the shared `SchemasRepository` (singleton) into `components.schemas`.
3. `OpenApiCustomizersRepository` — runs every registered `OpenApiCustomizer` over the final document.

Type resolution lives in `src/OpenApi/Util/Type/` and is the layer that bridges PHP reflection + PHPDoc to OpenAPI
schemas.

## Development

Tests are golden-file based: `OpenApiServiceTest` builds the full graph manually, serializes the resulting `OpenApi`,
and compares against `tests/OpenApi/expected-openapi.json`. Fixtures under `tests/Fixtures/` (notably `SampleController`
and `SampleClass`) are the canonical examples of every supported PHP type → OpenAPI shape.

```bash
# Inside a PHP 8.4+ environment with Composer
composer install
vendor/bin/phpunit tests
```

## License

MIT
