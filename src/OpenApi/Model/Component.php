<?php

namespace Ouzo\OpenApi\Model;

class Component
{
    private string $type;
    /** @var Schema[] */
    private ?array $properties = null;
    private ?array $required = null;
    private ?array $allOf = null;
    private ?array $oneOf = null;
    private ?Discriminator $discriminator = null;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Component
    {
        $this->type = $type;
        return $this;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(?array $properties): Component
    {
        $this->properties = $properties;
        return $this;
    }

    public function getRequired(): ?array
    {
        return $this->required;
    }

    public function setRequired(?array $required): Component
    {
        $this->required = $required;
        return $this;
    }

    public function getAllOf(): ?array
    {
        return $this->allOf;
    }

    public function setAllOf(?array $allOf): Component
    {
        $this->allOf = $allOf;
        return $this;
    }

    public function getOneOf(): ?array
    {
        return $this->oneOf;
    }

    public function setOneOf(?array $oneOf): Component
    {
        $this->oneOf = $oneOf;
        return $this;
    }

    public function getDiscriminator(): ?Discriminator
    {
        return $this->discriminator;
    }

    public function setDiscriminator(?Discriminator $discriminator): Component
    {
        $this->discriminator = $discriminator;
        return $this;
    }
}
