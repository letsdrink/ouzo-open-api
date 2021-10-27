<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Cache;
use Ouzo\Utilities\FluentArray;

class CachedInternalPathProvider
{
    #[Inject]
    public function __construct(
        private RoutesProvider $routesProvider,
        private InternalPathFactory $internalPathFactory
    )
    {
    }

    /** @return InternalPath[] */
    public function get(): array
    {
        return Cache::memoize(function () {
            $routeRules = $this->routesProvider->get();
            return FluentArray::from($routeRules)
                ->map(fn(RouteRule $r) => $this->internalPathFactory->create($r))
                ->filterNotBlank()
                ->toArray();
        });
    }
}
