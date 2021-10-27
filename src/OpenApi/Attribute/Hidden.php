<?php

namespace Ouzo\OpenApi\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Hidden
{
}
