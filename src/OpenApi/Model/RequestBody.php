<?php

namespace Ouzo\OpenApi\Model;

class RequestBody
{
    private array $content;
    private bool $required;

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): RequestBody
    {
        $this->content = $content;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): RequestBody
    {
        $this->required = $required;
        return $this;
    }
}
