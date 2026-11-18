<?php

namespace Ouzo\Fixtures\Traits;

use Ouzo\Fixtures\ChildClass;

class ClassOverridingTraitMembers
{
    use WithTags;

    /** @var null|ChildClass[] */
    private ?array $tags = null;

    /** @return ChildClass[] */
    public function getTags(): array
    {
        return [];
    }
}
