<?php

namespace Ouzo\OpenApi\Model\Media;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#schemaObject
 */
class Schema
{
    /** @var string[]|null */
    private ?array $required = null;

    private ?string $type = null;

    /** @var array<string, Schema>|null */
    private ?array $properties = null;

    #[SerializedName('$ref')]
    private ?string $ref = null;

    private ?bool $nullable = null;

    private ?Discriminator $discriminator = null;

    /** @return string[]|null */
    public function getRequired(): ?array
    {
        return $this->required;
    }

    /** @param string[]|null $required */
    public function setRequired(?array $required): static
    {
        $this->required = $required;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /** @return array<string, Schema>|null */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /** @param array<string, Schema>|null $properties */
    public function setProperties(?array $properties): static
    {
        $this->properties = $properties;
        return $this;
    }

    public function addProperties(string $key, Schema $property): static
    {
        $this->properties[$key] = $property;
        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): static
    {
        $this->ref = $ref;
        return $this;
    }

    public function getNullable(): ?bool
    {
        return $this->nullable;
    }

    public function setNullable(?bool $nullable): static
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function getDiscriminator(): ?Discriminator
    {
        return $this->discriminator;
    }

    public function setDiscriminator(?Discriminator $discriminator): static
    {
        $this->discriminator = $discriminator;
        return $this;
    }
}
