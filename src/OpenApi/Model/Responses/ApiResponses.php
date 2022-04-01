<?php

namespace Ouzo\OpenApi\Model\Responses;

use JsonSerializable;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#responses-object
 */
class ApiResponses implements JsonSerializable
{
    /** @var array<string, ApiResponse>|null */
    private ?array $apiResponses = null;

    /** @return array<string, ApiResponse>|null */
    public function getApiResponses(): ?array
    {
        return $this->apiResponses;
    }

    /** @param array<string, ApiResponse>|null $apiResponses */
    public function setApiResponses(?array $apiResponses): static
    {
        $this->apiResponses = $apiResponses;
        return $this;
    }

    public function addApiResponse(string $name, ApiResponse $apiResponse): static
    {
        $this->apiResponses[$name] = $apiResponse;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->apiResponses;
    }
}
