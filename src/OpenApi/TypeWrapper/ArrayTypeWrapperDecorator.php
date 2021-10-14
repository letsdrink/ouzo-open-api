<?php

namespace Ouzo\OpenApi\TypeWrapper;

class ArrayTypeWrapperDecorator implements TypeWrapper
{
    public function __construct(private TypeWrapper $typeWrapper)
    {
    }

    public function isPrimitive(): bool
    {
        return $this->typeWrapper->isPrimitive();
    }

    public function isArray(): bool
    {
        return true;
    }

    public function get()
    {
        return $this->typeWrapper->get();
    }
}
