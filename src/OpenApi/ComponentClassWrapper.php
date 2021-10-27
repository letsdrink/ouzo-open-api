<?php

namespace Ouzo\OpenApi;

use ReflectionClass;

class ComponentClassWrapper
{
    public function __construct(
        private ReflectionClass $reflectionClass,
        private ?ReflectionClass $allOfReflectionClass = null
    )
    {
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function getAllOfReflectionClass(): ?ReflectionClass
    {
        return $this->allOfReflectionClass;
    }
}
