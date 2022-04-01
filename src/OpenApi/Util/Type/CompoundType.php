<?php

namespace Ouzo\OpenApi\Util\Type;

class CompoundType
{
    public const ARRAY = 'array';
    public const OBJECT = 'object';

    /** @return string[] */
    public static function all(): array
    {
        return [self::ARRAY, self::OBJECT];
    }
}
