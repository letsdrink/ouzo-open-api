<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalProperty;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\Util\DocCommentTypeHelper;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class PropertiesExtractor
{
    /** @return InternalProperty[] */
    public function extract(ReflectionClass $reflectionClass): array
    {
        $internalProperties = [];

        $reflectionProperties = self::getAllProperties($reflectionClass);
        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionType = $reflectionProperty->getType();

            if (is_null($reflectionType)) {
                $typeWrapper = new PrimitiveTypeWrapper('string');
                $internalProperties[] = new InternalProperty($reflectionProperty->getName(), $reflectionClass, $typeWrapper);
                continue;
            }

            if ($reflectionType instanceof ReflectionUnionType) {
                /** @var ReflectionNamedType $reflectionNamedTypes */
                $reflectionType = Arrays::first($reflectionType->getTypes());
            }

            $type = $reflectionType->getName();
            if ($reflectionType->isBuiltin() && !in_array($type, ['array', 'object'])) {
                $type = TypeConverter::convertPrimitiveToOpenApiType($type);
                $typeWrapper = new PrimitiveTypeWrapper($type);
            } else {
                if ($type === 'array') {
                    $forProperty = DocCommentTypeHelper::getForProperty($reflectionProperty, 'string');
                    $type = TypeConverter::convertPrimitiveToOpenApiType($forProperty);
                    if (is_null($type)) {
                        $tmp = new ReflectionClass($forProperty);
                        $typeWrapper = new ComplexTypeWrapper($tmp);

                        $internalProperties = array_merge($internalProperties, $this->extract($tmp));
                    } else {
                        $typeWrapper = new PrimitiveTypeWrapper($type);
                    }
                    $typeWrapper = new ArrayTypeWrapperDecorator($typeWrapper);
                } else {
                    $tmp = new ReflectionClass($type);
                    $typeWrapper = new ComplexTypeWrapper($tmp);

                    $internalProperties = array_merge($internalProperties, $this->extract($tmp));
                }
            }
            $internalProperties[] = new InternalProperty($reflectionProperty->getName(), $reflectionClass, $typeWrapper);
        }

        return $internalProperties;
    }

    /** @return ReflectionProperty[] */
    private function getAllProperties(ReflectionClass $class): array
    {
        $result = [];
        do {
            $result = array_merge($result, $class->getProperties());
        } while ($class = $class->getParentClass());
        return $result;
    }
}
