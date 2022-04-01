<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\OpenApi\Model\Parameters\Parameter;
use Ouzo\OpenApi\Model\Parameters\RequestBody;
use Ouzo\OpenApi\Model\Responses\ApiResponses;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#operationObject
 */
class Operation
{
    /** @var string[] */
    private ?array $tags = null;

    private ?string $summary = null;

    private ?string $operationId = null;

    /** @var Parameter[]|null */
    private ?array $parameters = null;

    private ?RequestBody $requestBody = null;

    private ?ApiResponses $responses = null;

    /** @return string[]|null */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /** @param string[]|null $tags */
    public function setTags(?array $tags): static
    {
        $this->tags = $tags;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;
        return $this;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setOperationId(?string $operationId): static
    {
        $this->operationId = $operationId;
        return $this;
    }

    /** @return Parameter[]|null */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /** @param Parameter[]|null $parameters */
    public function setParameters(?array $parameters): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getRequestBody(): ?RequestBody
    {
        return $this->requestBody;
    }

    public function setRequestBody(?RequestBody $requestBody): static
    {
        $this->requestBody = $requestBody;
        return $this;
    }

    public function getResponses(): ?ApiResponses
    {
        return $this->responses;
    }

    public function setResponses(?ApiResponses $responses): static
    {
        $this->responses = $responses;
        return $this;
    }
}
