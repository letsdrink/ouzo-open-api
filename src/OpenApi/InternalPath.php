<?php

namespace Ouzo\OpenApi;

class InternalPath
{
    /** @param InternalParameter[]|null $internalParameters */
    public function __construct(
        private InternalPathDetails $internalPathDetails,
        private ?array $internalParameters,
        private ?InternalRequestBody $internalRequestBody,
        private InternalResponse $internalResponse
    )
    {
    }

    public function getInternalPathDetails(): InternalPathDetails
    {
        return $this->internalPathDetails;
    }

    /** @return InternalParameter[]|null */
    public function getInternalParameters(): ?array
    {
        return $this->internalParameters;
    }

    public function getInternalRequestBody(): ?InternalRequestBody
    {
        return $this->internalRequestBody;
    }

    public function getInternalResponse(): InternalResponse
    {
        return $this->internalResponse;
    }
}
