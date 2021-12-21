<?php

namespace Ouzo\OpenApi;

use ReflectionClass;

class InternalDiscriminator
{
    public function __construct(
        private string $name,
        private ReflectionClass $reflectionClass,
        private string $typeProperty
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function getTypeProperty(): string
    {
        return $this->typeProperty;
    }
}
