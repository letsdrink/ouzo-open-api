<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\Utilities\ToString\ToStringBuilder;
use Ouzo\Utilities\ToString\ToStringStyle;

class SimpleSchema implements Schema
{
    private string $type;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): SimpleSchema
    {
        $this->type = $type;
        return $this;
    }

    public function __toString(): string
    {
        return (new ToStringBuilder($this, ToStringStyle::shortPrefixStyle()))
            ->append('type', $this->type)
            ->toString();
    }
}
