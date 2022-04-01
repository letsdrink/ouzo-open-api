<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\OpenApi\Model\Info\Info;
use Ouzo\OpenApi\Model\Servers\Server;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#openapi-object
 */
class OpenApi
{
    private ?string $openapi = null;

    private ?Info $info = null;

    /** @var Server[]|null */
    private ?array $servers;

    private ?Paths $paths = null;

    private ?Components $components = null;

    public function getOpenapi(): ?string
    {
        return $this->openapi;
    }

    public function setOpenapi(?string $openapi): static
    {
        $this->openapi = $openapi;
        return $this;
    }

    public function getInfo(): ?Info
    {
        return $this->info;
    }

    public function setInfo(?Info $info): static
    {
        $this->info = $info;
        return $this;
    }

    /** @return Server[]|null */
    public function getServers(): ?array
    {
        return $this->servers;
    }

    /** @param Server[]|null $servers */
    public function setServers(?array $servers): static
    {
        $this->servers = $servers;
        return $this;
    }

    public function getPaths(): ?Paths
    {
        return $this->paths;
    }

    public function setPaths(?Paths $paths): static
    {
        $this->paths = $paths;
        return $this;
    }

    public function getComponents(): ?Components
    {
        return $this->components;
    }

    public function setComponents(?Components $components): static
    {
        $this->components = $components;
        return $this;
    }
}
