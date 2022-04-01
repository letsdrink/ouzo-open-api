<?php

namespace Ouzo\Fixtures;

use Ouzo\OpenApi\Attributes\Hidden;
use Ouzo\Utilities\Strings;

#[Hidden]
class HiddenController
{
    public function status(): string
    {
        return Strings::EMPTY_STRING;
    }
}
