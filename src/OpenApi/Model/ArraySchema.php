<?php

namespace Ouzo\OpenApi\Model;

use Ouzo\OpenApi\TypeWrapper\SwaggerType;

class ArraySchema implements Schema
{
    private Schema $items;

    public function getType(): string
    {
        return SwaggerType::ARRAY;
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
