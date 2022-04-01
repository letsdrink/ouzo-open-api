<?php

namespace Ouzo\OpenApi\Model\Media;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#mediaTypeObject
 */
class MediaType
{
    private ?Schema $schema = null;

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
