<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\Attribute\Schema;
use Ouzo\OpenApi\InternalProperty;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexType;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveType;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\OpenApiType;
use Ouzo\OpenApi\Util\DocCommentTypeHelper;
use Ouzo\OpenApi\Util\ReflectionUtils;
use Ouzo\OpenApi\Util\Set;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;

class PropertiesExtractor
{
    /** @return InternalProperty[] */
    public function extract(ReflectionClass $reflectionClass, bool $includeParentProperties = true): array
    {
        $internalProperties = new Set();

        $reflectionProperties = $this->getReflectionProperties($reflectionClass, $includeParentProperties);
        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionType = $reflectionProperty->getType();

            $schema = $this->getSchemaAttribute($reflectionProperty);

            if (is_null($reflectionType)) {
                $typeWrapper = new PrimitiveTypeWrapper(OpenApiType::STRING);
                $internalProperty = new InternalProperty($reflectionProperty->getName(), $reflectionClass, $typeWrapper, $schema);
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
            $internalProperty = new InternalProperty($reflectionProperty->getName(), $reflectionClass, $typeWrapper, $schema);
            $internalProperties->add($internalProperty);
        }

        return $internalProperties->all();
    }

    /** @return ReflectionProperty[] */
    private function getReflectionProperties(ReflectionClass $reflectionClass, bool $includeParentProperties): array
    {
        return $includeParentProperties ?
            ReflectionUtils::getAllProperties($reflectionClass) :
            ReflectionUtils::getProperties($reflectionClass);
    }

    private function getSchemaAttribute(ReflectionProperty $reflectionProperty): ?Schema
    {
        $reflectionAttributes = $reflectionProperty->getAttributes(Schema::class);
        if (empty($reflectionAttributes)) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $reflectionAttributes[0]->newInstance();
    }
}
