<?php

namespace Ouzo\OpenApi;

use ReflectionClass;

class InternalRequestBody
{
    public function __construct(
        private string $mimeType,
        private ?ReflectionClass $reflectionClass
    )
    {
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getReflectionClass(): ?ReflectionClass
    {
        return $this->reflectionClass;
    }
}
