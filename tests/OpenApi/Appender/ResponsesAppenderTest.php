<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\OpenApi\HiddenChecker;
use Ouzo\OpenApi\InternalPathFactory;
use Ouzo\OpenApi\Model\Path;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\Response;
use Ouzo\OpenApi\OperationIdGenerator;
use Ouzo\OpenApi\OperationIdRepository;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Chain\Chain;
use PHPUnit\Framework\TestCase;

class ResponsesAppenderTest extends TestCase
{
    private ResponsesAppender $responsesAppender;

    private Chain|MockInterface $chain;
    private InternalPathFactory $internalPathFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->responsesAppender = new ResponsesAppender();

        $this->chain = Mock::create(Chain::class);
        $this->internalPathFactory = new InternalPathFactory(
            new HiddenChecker(),
            new UriParametersExtractor(),
            new RequestBodyExtractor(),
            new ResponseExtractor(),
            new OperationIdGenerator(new OperationIdRepository())
        );
    }

    /**
     * @test
     */
    public function shouldAddResponses()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1', UsersController::class, 'info', true);

        $path = new Path();
        $pathContext = new PathContext($path, $this->internalPathFactory->create($routeRule));

        //when
        $this->responsesAppender->handle($pathContext, $this->chain);

        //then
        /** @var Response $response */
        $response = $path->getResponses()['200'];
        $this->assertSame('success', $response->getDescription());
        /** @var RefSchema $schema */
        $schema = $response->getContent()['application/json']['schema'];
        $this->assertSame('#/components/schemas/User', $schema->getRef());

        Mock::verify($this->chain)->proceed($pathContext);
    }

    /**
     * @test
     */
    public function shouldAddOnlyResponseCodeWhenCannotAppendReturn()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1/:id', UsersController::class, 'details', true);

        $path = new Path();
        $pathContext = new PathContext($path, $this->internalPathFactory->create($routeRule));

        //when
        $this->responsesAppender->handle($pathContext, $this->chain);

        //then
        /** @var Response $response */
        $response = $path->getResponses()['200'];
        $this->assertSame('success', $response->getDescription());
        $this->assertNull($response->getContent());

        Mock::verify($this->chain)->proceed($pathContext);
    }
}
