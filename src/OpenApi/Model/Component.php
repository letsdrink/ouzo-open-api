<?php

namespace Ouzo\OpenApi\Model;

class Component
{
    private string $type;
    /** @var Schema[] */
    private array $properties;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Component
    {
        $this->type = $type;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): Component
    {
        $this->properties = $properties;
        return $this;
    }
}
