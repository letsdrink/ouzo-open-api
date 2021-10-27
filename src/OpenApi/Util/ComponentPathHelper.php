<?php

namespace Ouzo\OpenApi\Util;

use ReflectionClass;

class ComponentPathHelper
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function getPathForReflectionClass(ReflectionClass $class): string
    {
        return "#/components/schemas/{$class->getShortName()}";
    }
}
