<?php

namespace Ouzo\OpenApi\Util;

use ReflectionClass;
use ReflectionProperty;

class ReflectionUtils
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    /** @return ReflectionProperty[] */
    public static function getAllProperties(ReflectionClass $class): array
    {
        $result = [];
        do {
            $result = array_merge($result, self::getProperties($class));
        } while ($class = $class->getParentClass());
        return $result;
    }

    /** @return ReflectionProperty[] */
    public static function getProperties(ReflectionClass $class): array
    {
        return $class->getProperties();
    }
}
