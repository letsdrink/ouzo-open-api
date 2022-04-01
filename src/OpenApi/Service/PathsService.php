<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Http\HttpMethod;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Model\Operation;
use Ouzo\OpenApi\Model\PathItem;
use Ouzo\OpenApi\Model\Paths;
use Ouzo\OpenApi\RouteRulesProvider;
use Ouzo\OpenApi\Util\UriUtils;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;

class PathsService
{
    #[Inject]
    public function __construct(
        private RouteRulesProvider $routeRulesProvider,
        private HiddenChecker $hiddenChecker,
        private OperationService $operationService
    )
    {
    }

    public function create(): ?Paths
    {
        $paths = null;

        $routeRules = $this->routeRulesProvider->get();
        foreach ($routeRules as $routeRule) {
            if ($this->hiddenChecker->isHidden($routeRule)) {
                continue;
            }

            if (is_null($paths)) {
                $paths = new Paths();
            }

            $this->addPathItem($paths, $routeRule);
        }

        return $paths;
    }

    private function addPathItem(Paths $paths, RouteRule $routeRule): void
    {
        $uri = UriUtils::sanitizeUri($routeRule);
        $httpMethod = $routeRule->getMethod();

        $pathItem = $this->getPathItem($paths, $uri);

        $operation = $this->operationService->create($routeRule);
        $this->setOperationType($httpMethod, $pathItem, $operation);

        $paths->addPathItem($uri, $pathItem);
    }

    private function getPathItem(Paths $paths, string $uri): PathItem
    {
        $pathItems = $paths->getPathItems();
        return is_null($pathItems) ? new PathItem() : Arrays::getValue($pathItems, $uri, new PathItem());
    }

    private function setOperationType(string $httpMethod, PathItem $pathItem, Operation $operation): void
    {
        if ($httpMethod === HttpMethod::GET) {
            $pathItem->setGet($operation);
        } else if ($httpMethod === HttpMethod::POST) {
            $pathItem->setPost($operation);
        } else if ($httpMethod === HttpMethod::PUT) {
            $pathItem->setPut($operation);
        } else if ($httpMethod === HttpMethod::DELETE) {
            $pathItem->setDelete($operation);
        }
    }
}
