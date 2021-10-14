<?php

namespace Ouzo\OpenApi\Model;

class Info
{
    private string $title;
    private string $description;
    private string $version;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Info
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Info
    {
        $this->description = $description;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): Info
    {
        $this->version = $version;
        return $this;
    }
}
