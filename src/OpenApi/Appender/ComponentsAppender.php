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
use Ouzo\Utilities\Arrays;
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
            $classNameToParametersToSchema = [];
            $classNameToRequired = null;
            $discriminator = $this->getDiscriminator($reflectionClass);

            $internalProperties = $internalClass->getInternalProperties();
            foreach ($internalProperties as $internalProperty) {
                $parameterName = $internalProperty->getName();

                $shortName = $internalProperty->getReflectionDeclaringClass()->getShortName();
                $classNameToParametersToSchema[$shortName][$parameterName] = TypeConverter::convertTypeWrapperToSchema($internalProperty->getTypeWrapper());

                $schemaAttribute = $internalProperty->getSchema();
                if ($schemaAttribute?->isRequired()) {
                    $classNameToRequired[$shortName][] = $parameterName;
                }
            }

            $allOfReflectionClass = $classWrapper->getAllOfReflectionClass();
            if (!is_null($allOfReflectionClass)) {

                $refSchema = (new RefSchema())
                    ->setRef(ComponentPathHelper::getPathForReflectionClass($allOfReflectionClass));

                $tmpComponents = [];
                foreach ($classNameToParametersToSchema as $parameterToSchema) {
                    $tmpComponents = (new Component())
                        ->setType(OpenApiType::OBJECT)
                        ->setProperties($parameterToSchema);
                }

                $components[$name] = (new Component())
                    ->setType(OpenApiType::OBJECT)
                    ->setRequired($classNameToRequired)
                    ->setAllOf([
                        $refSchema,
                        $tmpComponents,
                    ]);
            } else {
                foreach ($classNameToParametersToSchema as $className => $parameterToSchema) {
                    $required = !is_null($classNameToRequired) ? Arrays::getValue($classNameToRequired, $className) : null;
                    $components[$className] = (new Component())
                        ->setType(OpenApiType::OBJECT)
                        ->setProperties($parameterToSchema)
                        ->setRequired($required)
                        ->setDiscriminator($discriminator);
                }
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
