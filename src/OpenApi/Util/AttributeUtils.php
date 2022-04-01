<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Utilities\Arrays;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class AttributeUtils
{
    public static function attributeExists(ReflectionClass|ReflectionMethod $reflection, string $attribute): bool
    {
        $hiddenClass = $reflection->getAttributes($attribute);
        return !empty($hiddenClass);
    }

    public static function find(array $reflectionAttributes, string $attributeName): ?object
    {
        /** @var ReflectionAttribute|null $attribute */
        $attribute = Arrays::find($reflectionAttributes, fn(ReflectionAttribute $reflectionAttribute) => $reflectionAttribute->getName() === $attributeName);
        return is_null($attribute) ? null : $attribute->newInstance();
    }
}
