<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\Extractor\PropertiesExtractor;
use Ouzo\OpenApi\InternalPath;
use Ouzo\OpenApi\InternalProperty;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;
use Ouzo\Utilities\FluentArray;
use ReflectionClass;

class ComponentsAppender implements Interceptor
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
            foreach ($internalProperties as $internalProperty) {
                $schema = TypeConverter::convertTypeWrapperToSchema($internalProperty->getTypeWrapper());
                $properties[$internalProperty->getName()] = $schema;
            }
            $components[$name] = [
                'type' => 'object',
                'properties' => $properties,
            ];
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
