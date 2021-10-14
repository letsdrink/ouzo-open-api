<?php

namespace Ouzo\OpenApi\TypeWrapper;

class PrimitiveTypeWrapper implements TypeWrapper
{
    public function __construct(private mixed $type)
    {
    }

    public function isPrimitive(): bool
    {
        return true;
    }

    public function isArray(): bool
    {
        return false;
    }

    public function get(): mixed
    {
        return $this->type;
    }
}
