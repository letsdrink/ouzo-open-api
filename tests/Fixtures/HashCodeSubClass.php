<?php

namespace Ouzo\Fixtures;

use Ouzo\OpenApi\Util\HashCodeBuilder;

class HashCodeSubClass
{
    public int $age;

    public function hashCode(): int
    {
        return (new HashCodeBuilder())
            ->append($this->age)
            ->toHashCode();
    }
}
