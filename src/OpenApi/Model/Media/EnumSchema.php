<?php

namespace Ouzo\OpenApi\Model\Media;

class EnumSchema extends Schema
{
    /** @var string[]|int[] */
    private array $enum = [];

    /** @return string[]|int[] */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /** @param string[]|int[] $enum */
    public function setEnum(array $enum): static
    {
        $this->enum = $enum;
        return $this;
    }
}
