<?php

namespace Ouzo\Fixtures;

use Ouzo\OpenApi\Attributes\Schema;

class Tag
{
    private string $tagName1;

    #[Schema(required: true)]
    private string $tagName2;

    #[Schema(required: false)]
    private string $tagName3;

    private ?int $position = null;
}
