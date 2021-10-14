<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class InternalPathFactory
{
    #[Inject]
    public function __construct(
        private UriParametersExtractor $uriParametersExtractor,
        private RequestBodyExtractor $requestBodyExtractor,
        private ResponseExtractor $responseExtractor
    )
    {
    }

    public function create(RouteRule $routeRule): InternalPath
    {
        $reflectionClass = new ReflectionClass($routeRule->getController());
        $reflectionMethod = $reflectionClass->getMethod($routeRule->getAction());
        $reflectionParameters = $reflectionMethod->getParameters();

        $httpMethod = $routeRule->getMethod();

        $details = $this->createInternalPathDetails($routeRule);
        $parameters = $this->uriParametersExtractor->extract($details->getUri(), $httpMethod, $reflectionParameters);
        $requestBody = $this->requestBodyExtractor->extract($reflectionParameters, $httpMethod);
        $response = $this->responseExtractor->extract($routeRule, $reflectionMethod);

        return new InternalPath($details, $parameters, $requestBody, $response);
    }

    private function createInternalPathDetails(RouteRule $routeRule): InternalPathDetails
    {
        $uri = $this->sanitizeUri($routeRule);
        $tag = str_replace('_', ' ', explode('/', $uri)[1]);
        $summary = str_replace('_', ' ', Strings::camelCaseToUnderscore($routeRule->getAction()));
        $id = "{$routeRule->getControllerName()}#{$routeRule->getAction()}";
        $method = strtolower($routeRule->getMethod());

        return new InternalPathDetails($uri, $tag, $summary, $id, $method);
    }

    private function sanitizeUri(RouteRule $routeRule): string
    {
        $uri = $routeRule->getUri();
        $uri = preg_replace('/:(.*?)\//', '{\1}/', $uri);
        return preg_replace('/:(.*?)$/', '{\1}', $uri);
    }
}
