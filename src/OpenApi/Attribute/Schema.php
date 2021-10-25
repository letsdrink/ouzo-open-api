<?php

namespace Ouzo\OpenApi\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Schema
{
    public function __construct(private bool $required = false)
    {
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
