<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\OpenApi\TypeWrapper\OpenApiType;
use Ouzo\Utilities\ToString\ToStringBuilder;
use Ouzo\Utilities\ToString\ToStringStyle;

class ArraySchema implements Schema
{
    private Schema $items;

    public function getType(): string
    {
        return OpenApiType::ARRAY;
    }

    public function getItems(): Schema
    {
        return $this->items;
    }

    public function setItems(Schema $items): ArraySchema
    {
        $this->items = $items;
        return $this;
    }

    public function __toString(): string
    {
        return (new ToStringBuilder($this, ToStringStyle::shortPrefixStyle()))
            ->append('type', $this->getType())
            ->append('items', $this->items)
            ->toString();
    }
}
