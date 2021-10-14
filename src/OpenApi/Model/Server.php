<?php

namespace Ouzo\OpenApi\Model;

class Server
{
    private string $url;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Server
    {
        $this->url = $url;
        return $this;
    }
}
