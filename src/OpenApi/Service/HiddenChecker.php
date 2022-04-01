<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\OpenApi\Attributes\Hidden;
use Ouzo\OpenApi\Util\AttributeUtils;
use Ouzo\Routing\RouteRule;
use ReflectionClass;

class HiddenChecker
{
    public function isHidden(RouteRule $routeRule): bool
    {
        $reflectionClass = new ReflectionClass($routeRule->getController());
        $hiddenExists = AttributeUtils::attributeExists($reflectionClass, Hidden::class);
        if ($hiddenExists) {
            return true;
        }

        $reflectionMethod = $reflectionClass->getMethod($routeRule->getAction());
        $hiddenExists = AttributeUtils::attributeExists($reflectionMethod, Hidden::class);
        if (!empty($hiddenExists)) {
            return true;
        }

        return false;
    }
}
