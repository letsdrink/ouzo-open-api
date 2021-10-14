<?php

namespace Ouzo\OpenApi\Model;

class OpenApi
{
    private string $openapi;
    private Info $info;
    /** @var Server[] */
    private array $servers;
    private array $paths;
    private ?array $components;

    public function getOpenapi(): string
    {
        return $this->openapi;
    }

    public function setOpenapi(string $openapi): OpenApi
    {
        $this->openapi = $openapi;
        return $this;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }

    public function setInfo(Info $info): OpenApi
    {
        $this->info = $info;
        return $this;
    }

    /** @return Server[] */
    public function getServers(): array
    {
        return $this->servers;
    }

    /** @param Server[] $servers */
    public function setServers(array $servers): OpenApi
    {
        $this->servers = $servers;
        return $this;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function setPaths(array $paths): OpenApi
    {
        $this->paths = $paths;
        return $this;
    }

    public function getComponents(): ?array
    {
        return $this->components;
    }

    public function setComponents(?array $components): OpenApi
    {
        $this->components = $components;
        return $this;
    }
}
