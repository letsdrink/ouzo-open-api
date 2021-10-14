<?php

namespace Ouzo\OpenApi\Model;

class Response
{
    private string $description;
    private ?array $content = null;

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Response
    {
        $this->description = $description;
        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): Response
    {
        $this->content = $content;
        return $this;
    }
}
