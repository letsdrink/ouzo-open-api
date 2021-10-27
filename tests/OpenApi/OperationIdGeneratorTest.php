<?php

namespace Ouzo\OpenApi;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\Routing\RouteRule;
use PHPUnit\Framework\TestCase;

class OperationIdGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGenerateForRouteRuleWhenExactlyOneIsGenerated()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', UsersController::class, 'show', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('show');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('show_1', $operationId);
    }

    /**
     * @test
     */
    public function shouldGenerateForRouteRuleWhenSimilarOperationIsGenerated()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', UsersController::class, 'show', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('show');
        $operationIdRepository->add('show_1');
        $operationIdRepository->add('show_2');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('show_3', $operationId);
    }

    /**
     * @test
     */
    public function shouldSaveGeneratedIdOnMultipleSimilarActions()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', UsersController::class, 'show', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('show');
        $operationIdRepository->add('show_1');
        $operationIdRepository->add('show_2');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);
        $operationIdGenerator->generateForRouteRule($routeRule);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('show_4', $operationId);
    }
}
