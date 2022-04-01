<?php

namespace Ouzo\OpenApi\Model\Parameters;

use Ouzo\OpenApi\Model\Media\Schema;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#parameterObject
 */
class Parameter
{
    private ?string $name = null;

    private ?string $in = null;

    private ?bool $required = null;

    private ?Schema $schema = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getIn(): ?string
    {
        return $this->in;
    }

    public function setIn(?string $in): static
    {
        $this->in = $in;
        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): static
    {
        $this->required = $required;
        return $this;
    }

    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    public function setSchema(?Schema $schema): static
    {
        $this->schema = $schema;
        return $this;
    }
}
