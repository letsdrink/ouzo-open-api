<?php

namespace Ouzo\OpenApi\TypeWrapper;

use ReflectionClass;

class ComplexTypeWrapper implements TypeWrapper
{
    public function __construct(private ?ReflectionClass $reflectionClass)
    {
    }

    public function isPrimitive(): bool
    {
        return false;
    }

    public function isArray(): bool
    {
        return false;
    }

    public function get(): ?ReflectionClass
    {
        return $this->reflectionClass;
    }
}
