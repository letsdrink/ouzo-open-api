<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\OpenApi\InternalPathFactory;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\Path;
use Ouzo\OpenApi\OperationIdGenerator;
use Ouzo\OpenApi\OperationIdRepository;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Mock\MethodCall;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Chain\Chain;
use PHPUnit\Framework\TestCase;

class PathsAppenderTest extends TestCase
{
    private CachedInternalPathProvider|MockInterface $cachedInternalPathProvider;
    private ParametersAppender|MockInterface $parametersAppender;
    private RequestBodyAppender|MockInterface $requestBodyAppender;
    private ResponsesAppender|MockInterface $responsesAppender;

    private PathsAppender $pathsAppender;

    private Chain|MockInterface $chain;
    private InternalPathFactory $internalPathFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->cachedInternalPathProvider = Mock::create(CachedInternalPathProvider::class);
        $this->parametersAppender = Mock::create(ParametersAppender::class);
        $this->requestBodyAppender = Mock::create(RequestBodyAppender::class);
        $this->responsesAppender = Mock::create(ResponsesAppender::class);

        $this->pathsAppender = new PathsAppender($this->cachedInternalPathProvider, $this->parametersAppender, $this->requestBodyAppender, $this->responsesAppender);

        $this->chain = Mock::create(Chain::class);
        $this->internalPathFactory = new InternalPathFactory(
            new UriParametersExtractor(),
            new RequestBodyExtractor(),
            new ResponseExtractor(),
            new OperationIdGenerator(new OperationIdRepository())
        );
    }

    /**
     * @test
     */
    public function shouldAddPathDetailsAndCallAppenders()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::POST, '/url1/:id', UsersController::class, 'update', true);

        Mock::when($this->cachedInternalPathProvider)->get()->thenReturn([
            $this->internalPathFactory->create($routeRule),
        ]);

        $parametersAppenderCalled = false;
        Mock::when($this->parametersAppender)->handle(Mock::anyArgList())->thenAnswer(function (MethodCall $methodCall) use (&$parametersAppenderCalled) {
            $parametersAppenderCalled = true;
            return $this->callNextInterceptorInMock($methodCall);
        });

        $requestBodyAppenderCalled = false;
        Mock::when($this->requestBodyAppender)->handle(Mock::anyArgList())->thenAnswer(function (MethodCall $methodCall) use (&$requestBodyAppenderCalled) {
            $requestBodyAppenderCalled = true;
            return $this->callNextInterceptorInMock($methodCall);
        });

        $responsesAppenderCalled = false;
        Mock::when($this->responsesAppender)->handle(Mock::anyArgList())->thenAnswer(function (MethodCall $methodCall) use (&$responsesAppenderCalled) {
            $responsesAppenderCalled = true;
            return $this->callNextInterceptorInMock($methodCall);
        });

        $openApi = new OpenApi();

        //when
        $this->pathsAppender->handle($openApi, $this->chain);

        //then
        /** @var Path $path */
        $path = $openApi->getPaths()['/url1/{id}']['post'];
        $this->assertSame(['users_controller'], $path->getTags());
        $this->assertSame('users controller update', $path->getSummary());
        $this->assertSame('update', $path->getOperationId());

        $this->assertTrue($parametersAppenderCalled);
        $this->assertTrue($requestBodyAppenderCalled);
        $this->assertTrue($responsesAppenderCalled);

        Mock::verify($this->chain)->proceed($openApi);
    }

    private function callNextInterceptorInMock(MethodCall $methodCall): mixed
    {
        /** @var OpenApi $openApi */
        $openApi = $methodCall->arguments[0];
        /** @var Chain $next */
        $next = $methodCall->arguments[1];
        return $next->proceed($openApi);
    }
}
