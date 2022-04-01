<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Fixtures\HiddenController;
use Ouzo\Fixtures\SampleController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\Service\OperationId\OperationIdGenerator;
use Ouzo\OpenApi\Service\OperationId\OperationIdRepository;
use Ouzo\OpenApi\WithRouteRulesProvider;
use Ouzo\Routing\RouteRule;
use PHPUnit\Framework\TestCase;

class PathsServiceTest extends TestCase
{
    use WithRouteRulesProvider;

    /**
     * @test
     */
    public function shouldSkippHiddenResources()
    {
        //given
        $routeRules = [
            new RouteRule(HttpMethod::GET, '/url1', HiddenController::class, 'status', true),
            new RouteRule(HttpMethod::GET, '/url2', SampleController::class, 'hiddenMethod', true),
        ];

        $schemasRepository = new SchemasRepository();

        $routesProvider = $this->getRouteRulesProvider($routeRules);
        $hiddenChecker = new HiddenChecker();

        $operationIdGenerator = new OperationIdGenerator(new OperationIdRepository());
        $parametersService = new ParametersService();
        $contentService = new ContentService($schemasRepository);
        $requestBodyService = new RequestBodyService($contentService);
        $operationService = new OperationService($operationIdGenerator, $parametersService, $requestBodyService, $contentService);

        $pathsService = new PathsService($routesProvider, $hiddenChecker, $operationService);

        //when
        $paths = $pathsService->create();

        //then
        $this->assertNull($paths);
    }
}
