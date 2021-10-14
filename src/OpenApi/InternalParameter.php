<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\TypeWrapper\TypeWrapper;

class InternalParameter
{
    public function __construct(
        private string $name,
        private string $description,
        private TypeWrapper $typeWrapper
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTypeWrapper(): TypeWrapper
    {
        return $this->typeWrapper;
    }
}
