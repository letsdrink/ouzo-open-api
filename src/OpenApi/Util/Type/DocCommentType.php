<?php

namespace Ouzo\OpenApi\Util\Type;

class DocCommentType
{
    public function __construct(
        private ?string $type,
        private ?string $class,
        private bool $nullable,
        private bool $array
    )
    {
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getClass(): ?string
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
}
