<?php

namespace Ouzo\OpenApi\Attributes;

use Attribute;

/**
 * Marks a given resource, class as hidden, skipping while reading / resolving.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Hidden
{
}
