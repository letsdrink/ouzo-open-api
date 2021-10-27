<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\Attribute\Hidden;
use Ouzo\Routing\RouteRule;
use ReflectionClass;

class HiddenChecker
{
    public function isHidden(RouteRule $routeRule): bool
    {
        $reflectionClass = new ReflectionClass($routeRule->getController());

        $hiddenClass = $reflectionClass->getAttributes(Hidden::class);
        if (!empty($hiddenClass)) {
            return true;
        }

        $reflectionMethod = $reflectionClass->getMethod($routeRule->getAction());
        $hiddenMethod = $reflectionMethod->getAttributes(Hidden::class);
        if (!empty($hiddenMethod)) {
            return true;
        }

        return false;
    }
}
