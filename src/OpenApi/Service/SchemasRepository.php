<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\OpenApi\Attributes;
use Ouzo\OpenApi\Model\Media\ArraySchema;
use Ouzo\OpenApi\Model\Media\ComposedSchema;
use Ouzo\OpenApi\Model\Media\Discriminator;
use Ouzo\OpenApi\Model\Media\EnumSchema;
use Ouzo\OpenApi\Model\Media\Schema;
use Ouzo\OpenApi\Util\AttributeUtils;
use Ouzo\OpenApi\Util\ReflectionUtils;
use Ouzo\OpenApi\Util\SchemaUtils;
use Ouzo\OpenApi\Util\Type\CompoundType;
use Ouzo\OpenApi\Util\Type\Type;
use Ouzo\OpenApi\Util\Type\TypeUtils;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

class SchemasRepository
{
    /** @var array<string, Schema> */
    private array $schemas = [];

    /** @var array<string, array<Type>> */
    private array $discriminatorClassToMappingClasses = [];

    public function add(Type $type, ?string $pathForReflectionClass = null): void
    {
        $reflectionClass = $type->getClass();

        $isNotClassOrAlreadyGenerated = is_null($reflectionClass) || key_exists($reflectionClass->getShortName(), $this->schemas);
        if ($isNotClassOrAlreadyGenerated) {
            return;
        }

        [$discriminator, $required] = $this->handleDiscriminatorMap($reflectionClass);

        $includeParentProperties = is_null($pathForReflectionClass);

        $schema = (new Schema())
            ->setType(CompoundType::OBJECT)
            ->setDiscriminator($discriminator);

        $reflectionProperties = ReflectionUtils::conditionallyGetProperties($reflectionClass, $includeParentProperties);
        foreach ($reflectionProperties as $reflectionProperty) {
            $typeProperty = TypeUtils::getForProperty($reflectionProperty);

            /** @var Attributes\Schema|null $schemaAttribute */
            $schemaAttribute = AttributeUtils::find($typeProperty->getAttributes(), Attributes\Schema::class);
            if (!is_null($schemaAttribute)) {
                if ($schemaAttribute->isRequired()) {
                    $required[] = $typeProperty->getName();
                }
            }

            $typeSchema = SchemaUtils::create($typeProperty);

            $propertyReflectionClass = $typeProperty->getClass();
            if (!is_null($propertyReflectionClass)) {
                $this->add($typeProperty);

                $schemaForDiscriminator = $this->getSchemaForDiscriminator($propertyReflectionClass, $typeProperty);
                if (!is_null($schemaForDiscriminator)) {
                    $typeSchema = $schemaForDiscriminator;
                }
            }

            $schema->addProperties($reflectionProperty->getName(), $typeSchema);
        }

        if (!empty($required)) {
            $required = array_values(array_unique($required));
            $schema->setRequired($required);
        }

        if (!$includeParentProperties) {
            $allOf = [(new Schema())
                ->setRef($pathForReflectionClass)];
            if (!empty($schema->getProperties())) {
                $allOf[] = $schema;
            }
            $schema = (new ComposedSchema())
                ->setAllOf($allOf);
        }

        if ($reflectionClass->isEnum()) {
            $reflectionEnum = new ReflectionEnum($reflectionClass->getName());
            if ($reflectionEnum->isBacked()) {
                $values = array_map(
                    fn (ReflectionEnumBackedCase $case) => $case->getBackingValue(),
                    $reflectionEnum->getCases()
                );
                $schemaType = TypeUtils::convertPhpTypeToOpenApiType($reflectionEnum->getBackingType()->getName());
                $schema = (new EnumSchema())->setType($schemaType)->setEnum($values);
            }
        }

        $this->schemas[$reflectionClass->getShortName()] = $schema;
    }

    public function isEmpty(): bool
    {
        return empty($this->schemas);
    }

    /** @return array<string, Schema> */
    public function all(): array
    {
        return $this->schemas;
    }

    private function handleDiscriminatorMap(ReflectionClass $reflectionClass): array
    {
        $required = [];
        $discriminator = null;

        /** @var DiscriminatorMap|null $discriminatorMapAttribute */
        $discriminatorMapAttribute = AttributeUtils::find($reflectionClass->getAttributes(), DiscriminatorMap::class);
        if (!is_null($discriminatorMapAttribute)) {
            $pathForReflectionClass = SchemaUtils::getPathForReflectionClass($reflectionClass);

            $propertyName = $discriminatorMapAttribute->getTypeProperty();
            $required[] = $propertyName;
            $discriminator = (new Discriminator())
                ->setPropertyName($propertyName);
            foreach ($discriminatorMapAttribute->getMapping() as $mappingName => $mappingClass) {
                $mappingReflectionClass = new ReflectionClass($mappingClass);
                $value = SchemaUtils::getPathForReflectionClass($mappingReflectionClass);
                $discriminator->addMapping($mappingName, $value);

                $type = new Type(null, CompoundType::OBJECT, $mappingReflectionClass, false, false, []);
                $this->discriminatorClassToMappingClasses[$reflectionClass->getShortName()][] = $type;

                $this->add($type, $pathForReflectionClass);
            }
        }

        return [$discriminator, $required];
    }

    private function getSchemaForDiscriminator(ReflectionClass $reflectionClass, Type $type): ?Schema
    {
        $mappingClasses = Arrays::getValue($this->discriminatorClassToMappingClasses, $reflectionClass->getShortName());

        if (is_null($mappingClasses)) {
            return null;
        }

        $typeSchema = (new ComposedSchema());
        foreach ($mappingClasses as $mappingClass) {
            $schema = SchemaUtils::create($mappingClass);
            $typeSchema->addOneOf($schema);
        }
        if ($type->isNullable()) {
            $typeSchema->setNullable(true);
        }

        if ($type->isArray()) {
            return (new ArraySchema())
                ->setItems($typeSchema);
        }

        return $typeSchema;
    }
}
