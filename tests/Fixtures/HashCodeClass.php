<?php

namespace Ouzo\Fixtures;

use Ouzo\OpenApi\Util\HashCodeBuilder;

class HashCodeClass
{
    public mixed $mixed;

    public function hashCode(): int
    {
        return (new HashCodeBuilder())
            ->append($this->mixed)
            ->toHashCode();
    }
}
