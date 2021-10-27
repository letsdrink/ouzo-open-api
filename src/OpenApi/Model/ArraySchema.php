<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\OpenApi\TypeWrapper\OpenApiType;

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
}
