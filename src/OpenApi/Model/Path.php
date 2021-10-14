<?php

namespace Ouzo\OpenApi\Model;

class Path
{
    /** @var string[] */
    private array $tags;
    private string $summary;
    private string $operationId;
    /** @var Parameter[] */
    private ?array $parameters;
    private ?array $requestBody;
    private array $responses;

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): Path
    {
        $this->tags = $tags;
        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): Path
    {
        $this->summary = $summary;
        return $this;
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

    public function setOperationId(string $operationId): Path
    {
        $this->operationId = $operationId;
        return $this;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(?array $parameters): Path
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getRequestBody(): ?array
    {
        return $this->requestBody;
    }

    public function setRequestBody(?array $requestBody): Path
    {
        $this->requestBody = $requestBody;
        return $this;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function setResponses(array $responses): Path
    {
        $this->responses = $responses;
        return $this;
    }
}
