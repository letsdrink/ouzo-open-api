<?php

namespace Ouzo\OpenApi\Util\Type;

class ScalarType
{
    public const BOOLEAN = 'bool';
    public const FLOAT = 'float';
    public const INTEGER = 'int';
    public const MIXED = 'mixed';
    public const STRING = 'string';

    /** @return string[] */
    public static function all(): array
    {
        return [self::BOOLEAN, self::FLOAT, self::INTEGER, self::MIXED, self::STRING];
    }

    public static function isScalar(string $type): bool
    {
        return in_array($type, self::all());
    }
}
