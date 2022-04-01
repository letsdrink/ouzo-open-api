<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\OpenApi\Attributes;
use Ouzo\OpenApi\Model\Media\ArraySchema;
use Ouzo\OpenApi\Model\Media\ComposedSchema;
use Ouzo\OpenApi\Model\Media\Schema;
use Ouzo\OpenApi\Util\Type\ScalarType;
use Ouzo\OpenApi\Util\Type\Type;
use Ouzo\OpenApi\Util\Type\TypeUtils;
use ReflectionClass;

class SchemaUtils
{
    public static function create(Type $type): ?Schema
    {
        $typeName = $type->getType();

        if ($typeName === 'void') {
            return null;
        }

        $isNullable = $type->isNullable();

        /** @var Attributes\Schema|null $schemaAttribute */
        $schemaAttribute = AttributeUtils::find($type->getAttributes(), Attributes\Schema::class);
        if (!is_null($schemaAttribute)) {
            $isNullable = $schemaAttribute->isNullable();
        }

        if (!$type->isArray() && is_null($type->getClass())) {
            return self::schemaForPrimitive($typeName, $isNullable);
        }


        if ($type->isArray()) {
            if (ScalarType::isScalar($typeName)) {
                $schema = self::schemaForPrimitive($typeName, false);
                $arraySchema = (new ArraySchema())
                    ->setItems($schema);

                if ($isNullable) {
                    $arraySchema->setNullable(true);
                }

                return $arraySchema;
            }

            $schema = self::schemaForClass($type);
            $arraySchema = (new ArraySchema())
                ->setItems($schema);

            if ($isNullable) {
                $arraySchema->setNullable(true);
            }

            return $arraySchema;
        }

        $schema = self::schemaForClass($type);

        if ($isNullable) {
            return (new ComposedSchema())
                ->setAllOf([$schema])
                ->setNullable(true);
        }

        return $schema;
    }

    public static function getPathForReflectionClass(ReflectionClass $class): string
    {
        return "#/components/schemas/{$class->getShortName()}";
    }

    private static function schemaForClass(Type $type): Schema
    {
        $reflectionClass = $type->getClass();
        return (new Schema())
            ->setRef(SchemaUtils::getPathForReflectionClass($reflectionClass));
    }

    private static function schemaForPrimitive(string $type, bool $nullable): Schema
    {
        $schemaType = TypeUtils::convertPhpTypeToOpenApiType($type);
        $schema = (new Schema())
            ->setType($schemaType);

        if ($nullable) {
            $schema->setNullable(true);
        }

        return $schema;
    }
}
