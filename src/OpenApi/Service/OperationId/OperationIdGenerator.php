<?php
declare(strict_types=1);

namespace Ouzo\OpenApi\Service\OperationId;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Strings;

readonly class OperationIdGenerator
{
    #[Inject]
    public function __construct(private OperationIdRepository $operationIdRepository)
    {
    }

    public function generateForRouteRule(RouteRule $routeRule): string
    {
        $action = $routeRule->getAction();
        $action = lcfirst(Strings::underscoreToCamelCase($action));

        $hasOperationId = $this->operationIdRepository->hasOperationId($action);
        if ($hasOperationId) {
            $lastOperationId = $this->operationIdRepository->getLastOperationId($action);
            $number = preg_replace('/\D/', Strings::EMPTY, $lastOperationId);
            $i = Strings::isNotBlank($number) ? $number + 1 : 1;

            $action = "{$action}_{$i}";
        }

        $this->operationIdRepository->add($action);
        return $action;
    }
}
