<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Http\HttpStatus;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Model\Operation;
use Ouzo\OpenApi\Model\Responses\ApiResponse;
use Ouzo\OpenApi\Model\Responses\ApiResponses;
use Ouzo\OpenApi\Service\OperationId\OperationIdGenerator;
use Ouzo\OpenApi\Util\UriUtils;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class OperationService
{
    #[Inject]
    public function __construct(
        private OperationIdGenerator $operationIdGenerator,
        private ParametersService $parametersService,
        private RequestBodyService $requestBodyService,
        private ContentService $contentService
    )
    {
    }

    public function create(RouteRule $routeRule): Operation
    {
        $uri = UriUtils::sanitizeUri($routeRule);

        $reflectionClass = new ReflectionClass($routeRule->getController());
        $reflectionMethod = $reflectionClass->getMethod($routeRule->getAction());
        $reflectionParameters = $reflectionMethod->getParameters();

        $httpMethod = $routeRule->getMethod();
        $action = Strings::camelCaseToUnderscore($routeRule->getAction());

        $tag = Strings::camelCaseToUnderscore($reflectionClass->getShortName());
        $summary = "{$this->removeUnderscore($tag)} {$this->removeUnderscore($action)}";
        $operationId = $this->operationIdGenerator->generateForRouteRule($routeRule);
        $responseCode = Arrays::getValue($routeRule->getOptions(), 'code', HttpStatus::OK);

        $parameters = [];
        $requestBody = null;

        $pathParameterNames = UriUtils::getPathParameterNames($uri);
        foreach ($reflectionParameters as $reflectionParameter) {
            $tmpParameters = $this->parametersService->create($reflectionParameter, $pathParameterNames, $httpMethod);
            if (!is_null($tmpParameters)) {
                $parameters = array_merge($parameters, $tmpParameters);
            }

            $requestBody = $this->requestBodyService->create($reflectionParameter, $httpMethod);
        }

        $parameters = empty($parameters) ? null : $parameters;

        $content = $this->contentService->create($reflectionMethod);

        return (new Operation())
            ->setTags([$tag])
            ->setSummary($summary)
            ->setOperationId($operationId)
            ->setParameters($parameters)
            ->setRequestBody($requestBody)
            ->setResponses((new ApiResponses())
                ->addApiResponse($responseCode, (new ApiResponse())
                    ->setDescription('success')
                    ->setContent($content)
                )
            );
    }

    private function removeUnderscore(?string $string): string
    {
        return str_replace('_', ' ', $string);
    }
}
