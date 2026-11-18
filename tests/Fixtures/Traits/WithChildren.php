<?php

namespace Ouzo\Fixtures\Traits;

use Ouzo\Fixtures\ChildClass;

trait WithChildren
{
    use WithTags;

    /** @var ChildClass[] */
    private array $children;
}
