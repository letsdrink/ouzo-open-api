<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\Extractor\PropertiesExtractor;
use Ouzo\OpenApi\InternalPath;
use Ouzo\OpenApi\InternalProperty;
use Ouzo\OpenApi\Model\Component;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\TypeWrapper\SwaggerType;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\FluentArray;
use ReflectionClass;

class ComponentsAppender implements OpenApiAppender
{
    #[Inject]
    public function __construct(
        private CachedInternalPathProvider $cachedInternalPathProvider,
        private PropertiesExtractor $propertiesExtractor
    )
    {
    }

    /** @param OpenApi $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $internalProperties = $this->getInternalProperties();

        /** @var InternalProperty[] $groupedInternalProperties */
        $groupedInternalProperties = Arrays::groupBy($internalProperties, fn(InternalProperty $property) => $property->getReflectionDeclaringClass()->getShortName());

        $components = [];
        foreach ($groupedInternalProperties as $name => $internalProperties) {
            $properties = [];
            $required = null;
            /** @var InternalProperty[] $internalProperties */
            foreach ($internalProperties as $internalProperty) {
                $parameterName = $internalProperty->getName();

                $properties[$parameterName] = TypeConverter::convertTypeWrapperToSchema($internalProperty->getTypeWrapper());

                $schemaAttribute = $internalProperty->getSchema();
                if ($schemaAttribute?->isRequired()) {
                    $required[] = $parameterName;
                }
            }
            $components[$name] = (new Component())
                ->setType(SwaggerType::OBJECT)
                ->setProperties($properties)
                ->setRequired($required);
        }

        if (!empty($components)) {
            $param->setComponents(['schemas' => $components]);
        }

        return $next->proceed($param);
    }

    /** @return InternalProperty[] */
    private function getInternalProperties(): array
    {
        $reflectionClasses = $this->getReflectionClasses();

        $internalProperties = [];
        foreach ($reflectionClasses as $reflectionClass) {
            $internalProperties = array_merge($internalProperties, $this->propertiesExtractor->extract($reflectionClass));
        }

        return $internalProperties;
    }

    /** @return ReflectionClass[] */
    private function getReflectionClasses(): array
    {
        $internalPaths = $this->cachedInternalPathProvider->get();

        $requestReflectionClasses = FluentArray::from($internalPaths)
            ->filter(fn(InternalPath $path) => !is_null($path->getInternalRequestBody()))
            ->map(fn(InternalPath $path) => $path->getInternalRequestBody()->getReflectionClass())
            ->toArray();
        $responseReflectionClasses = FluentArray::from($internalPaths)
            ->filter(fn(InternalPath $path) => !$path->getInternalResponse()->getTypeWrapper()?->isPrimitive())
            ->map(fn(InternalPath $path) => $path->getInternalResponse()->getTypeWrapper()?->get())
            ->toArray();

        $reflectionClasses = array_merge($requestReflectionClasses, $responseReflectionClasses);

        return FluentArray::from($reflectionClasses)
            ->filterNotBlank()
            ->unique()
            ->toArray();
    }
}
