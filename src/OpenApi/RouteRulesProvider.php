<?php

namespace Ouzo\OpenApi;

use Ouzo\Routing\RouteRule;

interface RouteRulesProvider
{
    /** @return RouteRule[] */
    public function get(): array;
}
