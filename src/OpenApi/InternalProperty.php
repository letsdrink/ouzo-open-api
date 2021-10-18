<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\TypeWrapper\TypeWrapper;
use ReflectionClass;

class InternalProperty
{
    public function __construct(
        private string $name,
        private ReflectionClass $reflectionDeclaringClass,
        private TypeWrapper $typeWrapper
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReflectionDeclaringClass(): ReflectionClass
    {
        return $this->reflectionDeclaringClass;
    }

    public function getTypeWrapper(): TypeWrapper
    {
        return $this->typeWrapper;
    }
}
