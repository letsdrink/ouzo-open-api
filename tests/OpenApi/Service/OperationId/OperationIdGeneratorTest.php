<?php

namespace Ouzo\OpenApi\Service\OperationId;

use Ouzo\Fixtures\SampleController;
use Ouzo\Http\HttpMethod;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;

class OperationIdGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGenerateForRouteRuleWhenExactlyOneIsGenerated()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', SampleController::class, 'scalarInReturn', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('scalarInReturn');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('scalarInReturn_1', $operationId);
    }

    /**
     * @test
     */
    public function shouldGenerateForRouteRuleWhenSimilarOperationIsGenerated()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', SampleController::class, 'scalarInReturn', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('scalarInReturn');
        $operationIdRepository->add('scalarInReturn_1');
        $operationIdRepository->add('scalarInReturn_2');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('scalarInReturn_3', $operationId);
    }

    /**
     * @test
     */
    public function shouldSaveGeneratedIdOnMultipleSimilarActions()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', SampleController::class, 'scalarInReturn', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('scalarInReturn');
        $operationIdRepository->add('scalarInReturn_1');
        $operationIdRepository->add('scalarInReturn_2');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);
        $operationIdGenerator->generateForRouteRule($routeRule);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('scalarInReturn_4', $operationId);
    }

    /**
     * @test
     */
    public function shouldSanitizeAction()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', SampleController::class, 'snake_name', true);

        $operationIdRepository = new OperationIdRepository();

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('snakeName', $operationId);
    }

    /**
     * @test
     */
    public function shouldGenerateCorrectOperationIdForSimilarActions()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', SampleController::class, 'update', true);

        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('update_identity');
        $operationIdRepository->add('update');

        $operationIdGenerator = new OperationIdGenerator($operationIdRepository);
        $operationIdGenerator->generateForRouteRule($routeRule);

        //when
        $operationId = $operationIdGenerator->generateForRouteRule($routeRule);

        //then
        $this->assertSame('update_2', $operationId);
        Assert::thatArray($operationIdRepository->all())->containsExactly('update_identity', 'update', 'update_1', 'update_2');
    }
}
