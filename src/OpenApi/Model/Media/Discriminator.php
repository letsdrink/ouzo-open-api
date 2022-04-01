<?php

namespace Ouzo\OpenApi\Model\Media;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#discriminatorObject
 */
class Discriminator
{
    private ?string $propertyName = null;

    /** @var array<string, string>|null */
    private ?array $mapping = null;

    public function getPropertyName(): ?string
    {
        return $this->propertyName;
    }

    public function setPropertyName(?string $propertyName): static
    {
        $this->propertyName = $propertyName;
        return $this;
    }

    /** @return array<string, string>|null */
    public function getMapping(): ?array
    {
        return $this->mapping;
    }

    /** @param array<string, string>|null $mapping */
    public function setMapping(?array $mapping): static
    {
        $this->mapping = $mapping;
        return $this;
    }

    public function addMapping(string $name, string $value): static
    {
        $this->mapping[$name] = $value;
        return $this;
    }
}
