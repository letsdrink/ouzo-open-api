<?php

namespace Ouzo\OpenApi\Model;

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
}
