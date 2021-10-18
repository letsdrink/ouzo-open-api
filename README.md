# Ouzo OpenAPI

This lib should be used for generate [OpenAPI](https://swagger.io/specification/) specyfication.

## Usage

Implement your own version of [RoutesProvider](https://github.com/letsdrink/ouzo-open-api/blob/master/src/OpenApi/RoutesProvider.php?plain=1) interface, which gives list of [RouteRule](https://github.com/letsdrink/ouzo/blob/master/src/Ouzo/Core/Routing/RouteRule.php?plain=1)'s for generating OpenAPI elements.

Use [OpenApiFactory](https://github.com/letsdrink/ouzo-open-api/blob/master/src/OpenApi/OpenApiFactory.php) to generate OpenApi model, which can be serialized through [symfony/serializer](https://github.com/symfony/serializer).
