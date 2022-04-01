<?php

namespace Ouzo\OpenApi\Model\Parameters;

use Ouzo\OpenApi\Model\Media\Content;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#requestBodyObject
 */
class RequestBody
{
    private ?Content $content = null;

    private ?bool $required = null;

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): static
    {
        $this->content = $content;
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
}
