<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
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

        $details = $this->createInternalPathDetails($routeRule, $reflectionClass);
        $parameters = $this->uriParametersExtractor->extract($details->getUri(), $httpMethod, $reflectionParameters);
        $requestBody = $this->requestBodyExtractor->extract($reflectionParameters, $httpMethod);
        $response = $this->responseExtractor->extract($routeRule, $reflectionMethod);

        return new InternalPath($details, $parameters, $requestBody, $response);
    }

    private function createInternalPathDetails(RouteRule $routeRule, ReflectionClass $reflectionClass): InternalPathDetails
    {
        $uri = $this->sanitizeUri($routeRule);
        $tag = Strings::camelCaseToUnderscore($reflectionClass->getShortName());
        $action = Strings::camelCaseToUnderscore($routeRule->getAction());
        $summary = "{$this->removeUnderscore($tag)} {$this->removeUnderscore($action)}";
        $id = $routeRule->getAction();
        $method = strtolower($routeRule->getMethod());

        return new InternalPathDetails($uri, $tag, $summary, $id, $method);
    }

    private function sanitizeUri(RouteRule $routeRule): string
    {
        $uri = $routeRule->getUri();
        $uri = preg_replace('/:(.*?)\//', '{\1}/', $uri);
        return preg_replace('/:(.*?)$/', '{\1}', $uri);
    }

    private function removeUnderscore(?string $string): string
    {
        return str_replace('_', ' ', $string);
    }
}
