<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\ReflectionClassesProvider;
use Ouzo\OpenApi\InternalClass;
use Ouzo\OpenApi\Extractor\ClassExtractor;
use Ouzo\OpenApi\Model\Component;
use Ouzo\OpenApi\Model\Discriminator;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\TypeWrapper\OpenApiType;
use Ouzo\OpenApi\Util\ComponentPathHelper;
use Ouzo\OpenApi\Util\Set;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Chain\Chain;

class ComponentsAppender implements OpenApiAppender
{
    #[Inject]
    public function __construct(
        private ReflectionClassesProvider $reflectionClassesProvider,
        private ClassExtractor $classExtractor
    )
    {
    }

    /** @param OpenApi $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $classes = $this->getAllClasses();

        $components = [];
        foreach ($classes as $class) {
            $reflectionClass = $class->getReflectionClass();

            $name = $reflectionClass->getShortName();
            $classNameToParametersToSchema = [];
            $classNameToRequired = null;

            $parameters = $class->getProperties();
            foreach ($parameters as $internalProperty) {
                $parameterName = $internalProperty->getName();

                $classNameToParametersToSchema[$name][$parameterName] = TypeConverter::convertTypeWrapperToSchema($internalProperty->getTypeWrapper());

                $schemaAttribute = $internalProperty->getSchema();
                if ($schemaAttribute?->isRequired()) {
                    $classNameToRequired[$name][] = $parameterName;
                }
            }

            $allOfReflectionClass = $class->getRef();
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
                    $s = $class->getDiscriminator();
                    $discriminator = null;
                    if (!is_null($s)) {
                        $a = [];
                        foreach ($s as $itemx) {
                            $a[$itemx->getName()] = ComponentPathHelper::getPathForReflectionClass($itemx->getReflectionClass());
                        }
                        $discriminator = (new Discriminator())
                            ->setPropertyName($itemx->getTypeProperty())
                            ->setMapping($a);
                    }
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
    private function getAllClasses(): array
    {
        $set = new Set();
        foreach ($this->reflectionClassesProvider->get() as $reflectionClass) {
            $set->addAll($this->classExtractor->extract($reflectionClass)->all());
        }
        return $set->all();
    }
}
