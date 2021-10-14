<?php

namespace Ouzo\OpenApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

class RefSchema implements Schema
{
    #[SerializedName('$ref')]
    private string $ref;

    public function getRef(): string
    {
        return $this->ref;
    }

    public function setRef(string $ref): RefSchema
    {
        $this->ref = $ref;
        return $this;
    }
}
