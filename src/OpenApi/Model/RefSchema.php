<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\Utilities\ToString\ToStringBuilder;
use Ouzo\Utilities\ToString\ToStringStyle;
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

    public function __toString(): string
    {
        return (new ToStringBuilder($this, ToStringStyle::shortPrefixStyle()))
            ->append('ref', $this->ref)
            ->toString();
    }
}
