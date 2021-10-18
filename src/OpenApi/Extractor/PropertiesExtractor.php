<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalProperty;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexType;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveType;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\SwaggerType;
use Ouzo\OpenApi\Util\DocCommentTypeHelper;
use Ouzo\OpenApi\Util\Set;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;

class PropertiesExtractor
{
    /** @return InternalProperty[] */
    public function extract(ReflectionClass $reflectionClass): array
    {
        $internalProperties = new Set();

        $reflectionProperties = self::getAllProperties($reflectionClass);
        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionType = $reflectionProperty->getType();

            if (is_null($reflectionType)) {
                $typeWrapper = new PrimitiveTypeWrapper(SwaggerType::STRING);
                $internalProperty = new InternalProperty($reflectionProperty->getName(), $reflectionClass, $typeWrapper);
                $internalProperties->add($internalProperty);
                continue;
            }

            if ($reflectionType instanceof ReflectionUnionType) {
                $reflectionType = Arrays::first($reflectionType->getTypes());
            }

            $type = $reflectionType->getName();
            if ($reflectionType->isBuiltin() && !in_array($type, [ComplexType::ARRAY, ComplexType::OBJECT])) {
                $type = TypeConverter::convertPrimitiveToOpenApiType($type);
                $typeWrapper = new PrimitiveTypeWrapper($type);
            } else {
                if ($type === ComplexType::ARRAY) {
                    $forProperty = DocCommentTypeHelper::getForProperty($reflectionProperty, PrimitiveType::STRING);
                    $type = TypeConverter::convertPrimitiveToOpenApiType($forProperty);
                    if (is_null($type)) {
                        $tmp = new ReflectionClass($forProperty);
                        $typeWrapper = new ComplexTypeWrapper($tmp);

                        $internalProperties->addAll($this->extract($tmp));
                    } else {
                        $typeWrapper = new PrimitiveTypeWrapper($type);
                    }
                    $typeWrapper = new ArrayTypeWrapperDecorator($typeWrapper);
                } else {
                    $tmp = new ReflectionClass($type);
                    $typeWrapper = new ComplexTypeWrapper($tmp);

                    $internalProperties->addAll($this->extract($tmp));
                }
            }
            $internalProperty = new InternalProperty($reflectionProperty->getName(), $reflectionClass, $typeWrapper);
            $internalProperties->add($internalProperty);
        }

        return $internalProperties->all();
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
