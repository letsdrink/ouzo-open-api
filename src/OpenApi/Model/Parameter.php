<?php

namespace Ouzo\OpenApi\Model;

class Parameter
{
    private string $name;
    private string $in;
    private ?string $description = null;
    private bool $required;
    private Schema $schema;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Parameter
    {
        $this->name = $name;
        return $this;
    }

    public function getIn(): string
    {
        return $this->in;
    }

    public function setIn(string $in): Parameter
    {
        $this->in = $in;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Parameter
    {
        $this->description = $description;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): Parameter
    {
        $this->required = $required;
        return $this;
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function setSchema(Schema $schema): Parameter
    {
        $this->schema = $schema;
        return $this;
    }
}
