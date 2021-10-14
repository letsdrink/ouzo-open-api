<?php

namespace Ouzo\OpenApi;

use Ouzo\Routing\RouteRule;

interface RoutesProvider
{
    /** @return RouteRule[] */
    public function get(): array;
}
