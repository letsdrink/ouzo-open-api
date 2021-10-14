<?php

namespace Ouzo\OpenApi\TypeWrapper;

interface TypeWrapper
{
    public function isPrimitive(): bool;

    public function isArray(): bool;

    public function get();
}
