<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Attribute\Schema;
use Ouzo\OpenApi\InternalClass;
use Ouzo\OpenApi\InternalProperty;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexType;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\OpenApiType;
use Ouzo\OpenApi\TypeWrapper\PrimitiveType;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\Util\DocCommentTypeHelper;
use Ouzo\OpenApi\Util\ReflectionUtils;
use Ouzo\OpenApi\Util\Set;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;

class ClassExtractor
{
    #[Inject]
    public function __construct(private DiscriminatorExtractor $discriminatorExtractor)
    {
    }

    public function extract(ReflectionClass $reflectionClass, ?Set $set = null, ?ReflectionClass $ref = null): Set
    {
        if (is_null($set)) {
            $set = new Set();
        }

        $p = [];

        $reflectionProperties = $this->getReflectionProperties($reflectionClass, is_null($ref));
        foreach ($reflectionProperties as $reflectionProperty) {
            $internalDiscriminators = null;
            $reflectionType = $reflectionProperty->getType();

            $schema = $this->getSchemaAttribute($reflectionProperty);

            if (is_null($reflectionType)) {
                $typeWrapper = new PrimitiveTypeWrapper(OpenApiType::STRING);
                $internalProperty = new InternalProperty($reflectionProperty->getName(), $typeWrapper, $schema);
                $p[] = $internalProperty;
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
                        $this->extract($tmp, $set);
                        $typeWrapper = new ArrayTypeWrapperDecorator(new ComplexTypeWrapper($tmp));
                        $internalDiscriminators = $this->discriminatorExtractor->extract($tmp);
                    } else {
                        $typeWrapper = new ArrayTypeWrapperDecorator(new PrimitiveTypeWrapper($type));
                    }
                } else {
                    $tmp = new ReflectionClass($type);
                    $this->extract($tmp, $set);
                    $typeWrapper = new ComplexTypeWrapper($tmp);
                    $internalDiscriminators = $this->discriminatorExtractor->extract($tmp);
                }
            }
            $p[] = new InternalProperty($reflectionProperty->getName(), $typeWrapper, $schema, $internalDiscriminators);
        }

        $discriminator = $this->discriminatorExtractor->extract($reflectionClass);

        if (!is_null($discriminator)) {
            foreach ($discriminator as $item) {
                $this->extract($item->getReflectionClass(), $set, $reflectionClass);
            }
        }

        $set->add(new InternalClass($reflectionClass, $p, $discriminator, $ref));
        return $set;
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
