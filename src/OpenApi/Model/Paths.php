<?php

namespace Ouzo\OpenApi\Model;

use JsonSerializable;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#pathsObject
 */
class Paths implements JsonSerializable
{
    /** @var array<string, PathItem>|null */
    private ?array $pathItems = null;

    /** @return array<string, PathItem>|null */
    public function getPathItems(): ?array
    {
        return $this->pathItems;
    }

    /** @param array<string, PathItem>|null $pathItems */
    public function setPathItems(?array $pathItems): static
    {
        $this->pathItems = $pathItems;
        return $this;
    }

    public function addPathItem(string $string, PathItem $param): static
    {
        $this->pathItems[$string] = $param;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->pathItems;
    }
}
