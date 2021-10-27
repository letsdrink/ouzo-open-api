<?php

namespace Ouzo\OpenApi\Model;

class Discriminator
{
    private string $propertyName;
    private array $mapping;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): Discriminator
    {
        $this->propertyName = $propertyName;
        return $this;
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function setMapping(array $mapping): Discriminator
    {
        $this->mapping = $mapping;
        return $this;
    }
}
