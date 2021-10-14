<?php

namespace Ouzo\OpenApi\Model;

class ArraySchema implements Schema
{
    private Schema $items;

    public function getType(): string
    {
        return 'array';
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
