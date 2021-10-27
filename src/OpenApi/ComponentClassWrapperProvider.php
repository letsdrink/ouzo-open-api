<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use ReflectionClass;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

class ComponentClassWrapperProvider
{
    #[Inject]
    public function __construct(private CachedInternalPathProvider $cachedInternalPathProvider)
    {
    }

    /** @return ComponentClassWrapper[] */
    public function get(): array
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

        /** @var ReflectionClass[] $reflectionClasses */
        $reflectionClasses = FluentArray::from($reflectionClasses)
            ->filterNotBlank()
            ->unique()
            ->toArray();

        $componentClassWrappers = [];
        foreach ($reflectionClasses as $reflectionClass) {
            $reflectionAttributes = $reflectionClass->getAttributes(DiscriminatorMap::class);
            $componentClassWrappers[] = new ComponentClassWrapper($reflectionClass);

            if (!empty($reflectionAttributes)) {
                $reflectionAttribute = $reflectionAttributes[0];
                /** @var DiscriminatorMap $discriminatorMap */
                $discriminatorMap = $reflectionAttribute->newInstance();
                $mapping = $discriminatorMap->getMapping();
                $classes = Arrays::values($mapping);
                foreach ($classes as $class) {
                    $componentClassWrappers[] = new ComponentClassWrapper(new ReflectionClass($class), $reflectionClass);
                }
            }
        }

        return $componentClassWrappers;
    }
}
