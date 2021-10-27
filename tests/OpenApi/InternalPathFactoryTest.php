<?php

namespace Ouzo\OpenApi;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\Http\HttpStatus;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class InternalPathFactoryTest extends TestCase
{
    private UriParametersExtractor|MockInterface $uriParametersExtractor;
    private RequestBodyExtractor|MockInterface $requestBodyExtractor;
    private ResponseExtractor|MockInterface $responseExtractor;

    private InternalPathFactory $internalPathFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->uriParametersExtractor = Mock::create(UriParametersExtractor::class);
        $this->requestBodyExtractor = Mock::create(RequestBodyExtractor::class);
        $this->responseExtractor = Mock::create(ResponseExtractor::class);

        Mock::when($this->responseExtractor)
            ->extract(Mock::anyArgList())
            ->thenReturn(new InternalResponse(HttpStatus::OK));

        $this->internalPathFactory = new InternalPathFactory(
            new HiddenChecker(),
            $this->uriParametersExtractor,
            $this->requestBodyExtractor,
            $this->responseExtractor,
            new OperationIdGenerator(new OperationIdRepository())
        );
    }

    /**
     * @test
     */
    public function shouldCreateInternalPathDetails()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', UsersController::class, 'show', true);

        //when
        $internalPath = $this->internalPathFactory->create($routeRule);

        //then
        $internalPathDetails = $internalPath->getInternalPathDetails();
        $this->assertSame('/url1/{id}', $internalPathDetails->getUri());
        $this->assertSame('users_controller', $internalPathDetails->getTag());
        $this->assertSame('users controller show', $internalPathDetails->getSummary());
        $this->assertSame('show', $internalPathDetails->getOperationId());
        $this->assertSame('get', $internalPathDetails->getHttpMethod());
    }

    /**
     * @test
     */
    public function shouldUseExtractors()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', UsersController::class, 'show', true);

        //when
        $internalPath = $this->internalPathFactory->create($routeRule);

        //then
        $this->assertInstanceOf(InternalPath::class, $internalPath);

        Mock::verify($this->uriParametersExtractor)->extract('/url1/{id}', HttpMethod::GET, []);
        Mock::verify($this->requestBodyExtractor)->extract([], HttpMethod::GET);
        Mock::verify($this->responseExtractor)->extract(HttpStatus::OK, Mock::argThat()->isInstanceOf(ReflectionMethod::class));
    }
}
