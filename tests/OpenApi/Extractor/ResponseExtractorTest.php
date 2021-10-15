<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\Fixtures\Tag;
use Ouzo\Fixtures\User;
use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpStatus;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ResponseExtractorTest extends TestCase
{
    private ReflectionClass $reflectionClass;
    private ResponseExtractor $responseExtractor;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionClass = new ReflectionClass(UsersController::class);
        $this->responseExtractor = new ResponseExtractor();
    }

    /**
     * @test
     */
    public function shouldGetResponseOnlyWithHttpResponseCodeWhenMethodHasNotReturnType()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('show');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::OK, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::OK, $internalResponse->getResponseCode());
        $this->assertNull($internalResponse->getTypeWrapper());
    }

    /**
     * @test
     */
    public function shouldGetResponseOnlyWithHttpResponseCodeWhenMethodHasVoidReturnType()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('update');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::NO_CONTENT, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::NO_CONTENT, $internalResponse->getResponseCode());
        $this->assertNull($internalResponse->getTypeWrapper());
    }

    /**
     * @test
     */
    public function shouldGetResponseForPrimitiveReturnType()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('status');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::OK, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::OK, $internalResponse->getResponseCode());
        $this->assertEquals(new PrimitiveTypeWrapper('string'), $internalResponse->getTypeWrapper());
    }

    /**
     * @test
     */
    public function shouldGetResponseForComplexReturnType()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('info');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::OK, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::OK, $internalResponse->getResponseCode());
        $expected = new ComplexTypeWrapper(new ReflectionClass(User::class));
        $this->assertEquals($expected, $internalResponse->getTypeWrapper());
    }

    /**
     * @test
     */
    public function shouldGetResponseForArrayWithPrimitiveReturnType()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('tagNames');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::OK, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::OK, $internalResponse->getResponseCode());
        $expected = new ArrayTypeWrapperDecorator(new PrimitiveTypeWrapper('string'));
        $this->assertEquals($expected, $internalResponse->getTypeWrapper());
    }

    /**
     * @test
     */
    public function shouldGetResponseForArrayWithComplexReturnType()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('tags');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::OK, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::OK, $internalResponse->getResponseCode());
        $expected = new ArrayTypeWrapperDecorator(new ComplexTypeWrapper(new ReflectionClass(Tag::class)));
        $this->assertEquals($expected, $internalResponse->getTypeWrapper());
    }

    /**
     * @test
     */
    public function shouldGetResponseForArrayWithoutDocsAndFallbackToString()
    {
        //given
        $reflectionMethod = $this->reflectionClass->getMethod('categories');

        //when
        $internalResponse = $this->responseExtractor->extract(HttpStatus::OK, $reflectionMethod);

        //then
        $this->assertSame(HttpStatus::OK, $internalResponse->getResponseCode());
        $expected = new ArrayTypeWrapperDecorator(new PrimitiveTypeWrapper('string'));
        $this->assertEquals($expected, $internalResponse->getTypeWrapper());
    }
}
