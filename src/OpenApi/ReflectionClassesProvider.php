<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Utilities\FluentArray;
use ReflectionClass;

class ReflectionClassesProvider
{
    #[Inject]
    public function __construct(private CachedInternalPathProvider $cachedInternalPathProvider)
    {
    }

    /** @return ReflectionClass[] */
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

        return FluentArray::from($reflectionClasses)
            ->filterNotBlank()
            ->unique()
            ->toArray();
    }
}
