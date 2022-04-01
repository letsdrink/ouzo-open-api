<?php

namespace Ouzo\OpenApi\Util\Type;

use Ouzo\OpenApi\Model\OpenApiType;
use Ouzo\Utilities\Arrays;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class TypeUtils
{
    public static function convertPhpTypeToOpenApiType(string $scalar): ?string
    {
        return match ($scalar) {
            CompoundType::OBJECT => OpenApiType::OBJECT,

            ScalarType::INTEGER => OpenApiType::INTEGER,
            ScalarType::BOOLEAN => OpenApiType::BOOLEAN,
            ScalarType::STRING, ScalarType::MIXED => OpenApiType::STRING,

            default => null,
        };
    }

    public static function getForParameter(ReflectionParameter $reflectionParameter): Type
    {
        $reflectionType = $reflectionParameter->getType();
        $name = $reflectionParameter->getName();
        return self::getForType($reflectionType, $name);
    }

    public static function getForProperty(ReflectionProperty $reflectionProperty): Type
    {
        $reflectionType = $reflectionProperty->getType();
        $name = $reflectionProperty->getName();
        $phpDocType = DocCommentTypeUtils::getForProperty($reflectionProperty, ScalarType::STRING);
        $attributes = $reflectionProperty->getAttributes();

        return self::getForType($reflectionType, $name, $phpDocType, $attributes);
    }

    public static function getForReturnType(ReflectionMethod $reflectionMethod): Type
    {
        $reflectionType = $reflectionMethod->getReturnType();
        if (is_null($reflectionType)) {
            return new Type(null, 'void', null, false, false, []);
        }

        $phpDocType = DocCommentTypeUtils::getForReturn($reflectionMethod);
        return self::getForType($reflectionType, null, $phpDocType);
    }

    /** @param ReflectionAttribute[] $attributes */
    private static function getForType(?ReflectionType $reflectionType, ?string $name, ?DocCommentType $docCommentType = null, array $attributes = []): Type
    {
        if (is_null($reflectionType)) {
            if (!is_null($docCommentType)) {
                $class = is_null($docCommentType->getClass()) ? null : new ReflectionClass($docCommentType->getClass());
                return new Type($name, $docCommentType->getType(), $class, $docCommentType->isNullable(), $docCommentType->isArray(), $attributes);
            }
            return new Type($name, ScalarType::STRING, null, true, false, $attributes);
        }

        $nullable = $reflectionType->allowsNull();

        if ($reflectionType instanceof ReflectionUnionType) {
            $reflectionType = Arrays::first($reflectionType->getTypes());
        }

        $typeName = $reflectionType->getName();

        if ($typeName === 'void') {
            return new Type($name, 'void', null, false, false, $attributes);
        }

        if ($reflectionType->isBuiltin() && ScalarType::isScalar($typeName)) {
            return new Type($name, $typeName, null, $nullable, false, $attributes);
        }

        if ($reflectionType->isBuiltin() && $typeName === CompoundType::OBJECT) {
            return new Type($name, CompoundType::OBJECT, null, $nullable, false, $attributes);
        }

        $isArray = false;
        if ($typeName === CompoundType::ARRAY) {
            $typeName = $docCommentType->getType() ?: ScalarType::STRING;
            $isArray = true;

            if (ScalarType::isScalar($typeName)) {
                return new Type($name, $typeName, null, $nullable, true, $attributes);
            }
            $typeName = $docCommentType->getClass();
        }

        $reflectionClass = new ReflectionClass($typeName);
        return new Type($name, CompoundType::OBJECT, $reflectionClass, $nullable, $isArray, $attributes);
    }
}
