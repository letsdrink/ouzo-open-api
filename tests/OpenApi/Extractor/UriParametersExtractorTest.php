<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\Fixtures\UserQueryRequest;
use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UriParametersExtractorTest extends TestCase
{
    private UriParametersExtractor $uriParametersExtractor;

    public function setUp(): void
    {
        parent::setUp();
        $this->uriParametersExtractor = new UriParametersExtractor();
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenThereAreNoParameters()
    {
        //given
        $uri = '/url';

        //when
        $internalParameters = $this->uriParametersExtractor->extract($uri, HttpMethod::GET, []);

        //then
        $this->assertNull($internalParameters);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenPathParametersAreNullAndHttpMethodIsNotGet()
    {
        //given
        $uri = '/url';

        //when
        $internalParameters = $this->uriParametersExtractor->extract($uri, HttpMethod::POST, []);

        //then
        $this->assertNull($internalParameters);
    }

    /**
     * @test
     */
    public function shouldGetPathParameters()
    {
        //given
        $reflectionClass = new ReflectionClass(UsersController::class);
        $reflectionMethod = $reflectionClass->getMethod('details');
        $reflectionParameters = $reflectionMethod->getParameters();

        $uri = '/url/{id}';

        //when
        $internalParameters = $this->uriParametersExtractor->extract($uri, HttpMethod::GET, $reflectionParameters);

        //then
        Assert::thatArray($internalParameters)
            ->extracting('getName()', 'getDescription()', 'getTypeWrapper()')
            ->containsOnly(['id', 'id', new PrimitiveTypeWrapper('integer')]);
    }

    /**
     * @test
     */
    public function shouldGetQueryParameters()
    {
        //given
        $reflectionClass = new ReflectionClass(UsersController::class);
        $reflectionMethod = $reflectionClass->getMethod('filter');
        $reflectionParameters = $reflectionMethod->getParameters();

        $uri = '/url';

        //when
        $internalParameters = $this->uriParametersExtractor->extract($uri, HttpMethod::GET, $reflectionParameters);

        //then
        Assert::thatArray($internalParameters)
            ->extracting('getName()', 'getDescription()', 'getTypeWrapper()')
            ->containsOnly(
                ['userQueryRequest', 'user query request', new ComplexTypeWrapper(new ReflectionClass(UserQueryRequest::class))]
            );
    }
}
