<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\OpenApi\InternalPathFactory;
use Ouzo\OpenApi\Model\Path;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Chain\Chain;
use PHPUnit\Framework\TestCase;

class RequestBodyAppenderTest extends TestCase
{
    private RequestBodyAppender $requestBodyAppender;

    private Chain|MockInterface $chain;
    private InternalPathFactory $internalPathFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBodyAppender = new RequestBodyAppender();

        $this->chain = Mock::create(Chain::class);
        $this->internalPathFactory = new InternalPathFactory(new UriParametersExtractor(), new RequestBodyExtractor(), new ResponseExtractor());
    }

    /**
     * @test
     */
    public function shouldAddRequestBody()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::POST, '/url1/:id', UsersController::class, 'update', true);

        $path = new Path();
        $pathContext = new PathContext($path, $this->internalPathFactory->create($routeRule));

        //when
        $this->requestBodyAppender->handle($pathContext, $this->chain);

        //then
        $requestBody = $path->getRequestBody();
        /** @var RefSchema $schema */
        $schema = $requestBody['content']['application/json']['schema'];
        $this->assertSame('#/components/schemas/UserRequest', $schema->getRef());

        Mock::verify($this->chain)->proceed($pathContext);
    }

    /**
     * @test
     */
    public function shouldNotAddRequestBodyWhenIsNotDefined()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::POST, '/url1', UsersController::class, 'show', true);

        $path = new Path();
        $pathContext = new PathContext($path, $this->internalPathFactory->create($routeRule));

        //when
        $this->requestBodyAppender->handle($pathContext, $this->chain);

        //then
        $this->assertNull($path->getRequestBody());

        Mock::verify($this->chain)->proceed($pathContext);
    }
}
