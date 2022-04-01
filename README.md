# Ouzo OpenAPI

This lib should be used for generate [OpenAPI](https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md) specyfication.

## Usage

Implement your own version
of [RouteRulesProvider](https://github.com/letsdrink/ouzo-open-api/blob/master/src/OpenApi/RoutesProvider.php?plain=1)
interface, which gives list
of [RouteRule](https://github.com/letsdrink/ouzo/blob/master/src/Ouzo/Core/Routing/RouteRule.php?plain=1)'s for
generating OpenAPI elements.
