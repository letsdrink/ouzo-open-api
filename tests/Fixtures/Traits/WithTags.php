<?php

namespace Ouzo\Fixtures\Traits;

use Ouzo\Fixtures\Tag;

trait WithTags
{
    /** @var null|Tag[] */
    private ?array $tags = null;

    /** @return Tag[] */
    public function getTags(): array
    {
        return $this->tags ?? [];
    }
}
