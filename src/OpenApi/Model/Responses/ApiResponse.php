<?php

namespace Ouzo\OpenApi\Model\Responses;

use Ouzo\OpenApi\Model\Media\Content;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#responseObject
 */
class ApiResponse
{
    private ?string $description = null;

    private ?Content $content = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): static
    {
        $this->content = $content;
        return $this;
    }
}
