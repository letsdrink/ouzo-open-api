<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\OpenApi\Model\ArraySchema;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\Schema;
use Ouzo\OpenApi\Model\SimpleSchema;
use Ouzo\OpenApi\TypeWrapper\TypeWrapper;

class TypeConverter
{
    private function __construct()
    {
    }

    public static function convertPrimitiveToSwaggerType(string $primitive): ?string
    {
        return match ($primitive) {
            'int' => 'integer',
            'bool' => 'boolean',
            'string', 'mixed' => 'string',
            default => null,
        };
    }

    public static function convertTypeWrapperToSchema(?TypeWrapper $typeWrapper): ?Schema
    {
        if (is_null($typeWrapper)) {
            return null;
        }

        $type = $typeWrapper->get();
        if (is_null($type)) {
            return null;
        }

        $schema = $typeWrapper->isPrimitive() ?
            (new SimpleSchema())->setType($type) :
            (new RefSchema())->setRef("#/components/schemas/{$type->getShortName()}");

        if ($typeWrapper->isArray()) {
            $schema = (new ArraySchema())->setItems($schema);
        }

        return $schema;
    }
}
