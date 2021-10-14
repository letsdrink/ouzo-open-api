<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\TypeWrapper\TypeWrapper;

class InternalResponse
{
    public function __construct(
        private int $responseCode,
        private ?TypeWrapper $typeWrapper = null
    )
    {
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getTypeWrapper(): ?TypeWrapper
    {
        return $this->typeWrapper;
    }
}
