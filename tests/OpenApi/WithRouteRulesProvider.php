<?php

namespace Ouzo\OpenApi;

use Ouzo\Routing\RouteRule;

trait WithRouteRulesProvider
{
    /** @param RouteRule[] $routeRules */
    public function getRouteRulesProvider(array $routeRules): RouteRulesProvider
    {
        return new class($routeRules) implements RouteRulesProvider {
            public function __construct(private array $routeRules)
            {
            }

            /** @return RouteRule[] */
            public function get(): array
            {
                return $this->routeRules;
            }
        };
    }
}
