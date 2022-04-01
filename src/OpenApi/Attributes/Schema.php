<?php

namespace Ouzo\OpenApi\Attributes;

use Attribute;

/**
 * The annotation may be used to define a Schema for a set of elements of the OpenAPI spec, and/or to define additional
 * properties for the schema.
 *
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#schemaObject
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Schema
{
    public function __construct(
        private bool $required = false,
        private bool $nullable = false
    )
    {
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
