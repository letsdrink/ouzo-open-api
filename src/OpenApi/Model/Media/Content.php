<?php

namespace Ouzo\OpenApi\Model\Media;

use JsonSerializable;

class Content implements JsonSerializable
{
    /** @var array<string, MediaType>|null */
    private ?array $mediaTypes = null;

    /** @return array<string, MediaType>|null */
    public function getMediaTypes(): ?array
    {
        return $this->mediaTypes;
    }

    /** @param array<string, MediaType>|null $mediaTypes */
    public function setMediaTypes(?array $mediaTypes): static
    {
        $this->mediaTypes = $mediaTypes;
        return $this;
    }

    public function addMediaType(string $name, MediaType $mediaType): static
    {
        $this->mediaTypes[$name] = $mediaType;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->mediaTypes;
    }
}
