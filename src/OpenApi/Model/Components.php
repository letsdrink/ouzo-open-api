<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\OpenApi\Model\Media\Schema;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#componentsObject
 */
class Components
{
    /** @var array<string, Schema>|null */
    private ?array $schemas = null;

    /** @return array<string, Schema>|null */
    public function getSchemas(): ?array
    {
        return $this->schemas;
    }

    /** @param array<string, Schema>|null $schemas */
    public function setSchemas(?array $schemas): static
    {
        $this->schemas = $schemas;
        return $this;
    }

    public function addSchemas(string $key, Schema $schema): static
    {
        $this->schemas[$key] = $schema;
        return $this;
    }
}
