<?php

namespace Ouzo\OpenApi;

class InternalPathDetails
{
    public function __construct(
        private string $uri,
        private string $tag,
        private string $summary,
        private string $operationId,
        private string $httpMethod
    )
    {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}
