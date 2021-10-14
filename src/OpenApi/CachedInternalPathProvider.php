<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Cache;

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
            return Arrays::map($routeRules, fn(RouteRule $r) => $this->internalPathFactory->create($r));
        });
    }
}
