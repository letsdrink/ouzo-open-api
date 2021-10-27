<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\ComponentClassWrapperProvider;
use Ouzo\OpenApi\Extractor\PropertiesExtractor;
use Ouzo\OpenApi\InternalClass;
use Ouzo\OpenApi\Model\Component;
use Ouzo\OpenApi\Model\Discriminator;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\TypeWrapper\OpenApiType;
use Ouzo\OpenApi\Util\ComponentPathHelper;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Chain\Chain;
use ReflectionClass;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

class ComponentsAppender implements OpenApiAppender
{
    #[Inject]
    public function __construct(
        private ComponentClassWrapperProvider $componentClassWrapperProvider,
        private PropertiesExtractor $propertiesExtractor
    )
    {
    }

    /** @param OpenApi $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $internalClasses = $this->getInternalClasses();

        $components = [];
        foreach ($internalClasses as $internalClass) {
            $classWrapper = $internalClass->getComponentClassWrapper();

            $reflectionClass = $classWrapper->getReflectionClass();
            $name = $reflectionClass->getShortName();
            $properties = [];
            $required = null;

            $internalProperties = $internalClass->getInternalProperties();
            foreach ($internalProperties as $internalProperty) {
                $parameterName = $internalProperty->getName();

                $properties[$parameterName] = TypeConverter::convertTypeWrapperToSchema($internalProperty->getTypeWrapper());

                $schemaAttribute = $internalProperty->getSchema();
                if ($schemaAttribute?->isRequired()) {
                    $required[] = $parameterName;
                }
            }

            $discriminator = $this->getDiscriminator($reflectionClass);

            $value = $classWrapper->getAllOfReflectionClass();
            if (is_null($value)) {
                $components[$name] = (new Component())
                    ->setType(OpenApiType::OBJECT)
                    ->setProperties($properties)
                    ->setRequired($required)
                    ->setDiscriminator($discriminator);
            } else {
                $refSchema = (new RefSchema())
                    ->setRef(ComponentPathHelper::getPathForReflectionClass($value));
                $component = (new Component())
                    ->setType(OpenApiType::OBJECT)
                    ->setProperties($properties);
                $components[$name] = (new Component())
                    ->setType(OpenApiType::OBJECT)
                    ->setRequired($required)
                    ->setAllOf([
                        $refSchema,
                        $component,
                    ]);
            }
        }

        if (!empty($components)) {
            $param->setComponents(['schemas' => $components]);
        }

        return $next->proceed($param);
    }

    /** @return InternalClass[] */
    private function getInternalClasses(): array
    {
        $componentClassWrappers = $this->componentClassWrapperProvider->get();

        $internalClasses = [];
        foreach ($componentClassWrappers as $componentClassWrapper) {
            $includeParentProperties = is_null($componentClassWrapper->getAllOfReflectionClass());
            $internalProperties = $this->propertiesExtractor->extract($componentClassWrapper->getReflectionClass(), $includeParentProperties);
            $internalClasses[] = new InternalClass($componentClassWrapper, $internalProperties);
        }

        return $internalClasses;
    }

    private function getDiscriminator(ReflectionClass $reflectionClass): ?Discriminator
    {
        $reflectionAttributes = $reflectionClass->getAttributes(DiscriminatorMap::class);

        if (empty($reflectionAttributes)) {
            return null;
        }

        $reflectionAttribute = $reflectionAttributes[0];
        /** @var DiscriminatorMap $discriminatorMap */
        $discriminatorMap = $reflectionAttribute->newInstance();
        $mapping = [];
        foreach ($discriminatorMap->getMapping() as $k => $v) {
            $rClass = new ReflectionClass($v);
            $mapping[$k] = ComponentPathHelper::getPathForReflectionClass($rClass);
        }

        return (new Discriminator())
            ->setPropertyName($discriminatorMap->getTypeProperty())
            ->setMapping($mapping);
    }
}
