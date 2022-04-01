<?php

namespace Ouzo\OpenApi\Util\Type;

use ReflectionAttribute;
use ReflectionClass;

class Type
{
    /** @param ReflectionAttribute[] $attributes */
    public function __construct(
        private ?string $name,
        private string $type,
        private ?ReflectionClass $class,
        private bool $nullable,
        private bool $array,
        private array $attributes
    )
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getClass(): ?ReflectionClass
    {
        return $this->class;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isArray(): bool
    {
        return $this->array;
    }

    /** @return ReflectionAttribute[] */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
