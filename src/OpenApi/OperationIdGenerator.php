<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Strings;

class OperationIdGenerator
{
    #[Inject]
    public function __construct(private OperationIdRepository $operationIdRepository)
    {
    }

    public function generateForRouteRule(RouteRule $routeRule): string
    {
        $action = $routeRule->getAction();

        $hasOperationId = $this->operationIdRepository->hasOperationId($action);
        if ($hasOperationId) {
            $lastOperationId = $this->operationIdRepository->getLastOperationId($action);
            $number = preg_replace('/\D/', Strings::EMPTY_STRING, $lastOperationId);
            $i = Strings::isNotBlank($number) ? $number + 1 : 1;

            $newAction = "{$action}_{$i}";
            $this->operationIdRepository->add($newAction);
            return $newAction;
        }

        $this->operationIdRepository->add($action);
        return $action;
    }
}
