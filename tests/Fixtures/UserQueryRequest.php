<?php

namespace Ouzo\Fixtures;

use Ouzo\OpenApi\Attribute\Schema;

class UserQueryRequest
{
    #[Schema(required: true)]
    private string $name;
    private int $age;
    private Tag $tag;
}
