<?php

namespace Ouzo\OpenApi\Model\Media;

class ArraySchema extends Schema
{
    private ?Schema $items = null;

    public function __construct()
    {
        $this->setType('array');
    }

    public function getItems(): ?Schema
    {
        return $this->items;
    }

    public function setItems(?Schema $items): ArraySchema
    {
        $this->items = $items;
        return $this;
    }
}
