<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\Fixtures\UserRequest;
use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RequestBodyExtractorTest extends TestCase
{
    private ReflectionClass $reflectionClass;
    private RequestBodyExtractor $requestBodyExtractor;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionClass = new ReflectionClass(UsersController::class);
        $this->requestBodyExtractor = new RequestBodyExtractor();
    }

    /**
     * @test
     */
    public function shouldExtract()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('update');
        $reflectionParameters = $reflectionMethod->getParameters();

        //when
        $internalRequestBody = $this->requestBodyExtractor->extract($reflectionParameters, HttpMethod::PUT);

        //then
        $this->assertSame('application/json', $internalRequestBody->getMimeType());
        $this->assertEquals(new ReflectionClass(UserRequest::class), $internalRequestBody->getReflectionClass());
    }

    /**
     * @test
     */
    public function shouldSkipExtractingWhenHttpMethodIsGet()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('filter');
        $reflectionParameters = $reflectionMethod->getParameters();

        //when
        $internalRequestBody = $this->requestBodyExtractor->extract($reflectionParameters, HttpMethod::GET);

        //then
        $this->assertNull($internalRequestBody);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenThereAreNoParameters()
    {
        //when
        $internalRequestBody = $this->requestBodyExtractor->extract([], HttpMethod::POST);

        //then
        $this->assertNull($internalRequestBody);
    }
}
