<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\Appender\ComponentsAppender;
use Ouzo\OpenApi\Appender\PathsAppender;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\OpenApiVersion;
use Ouzo\Tests\Mock\MethodCall;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Chain\Chain;
use PHPUnit\Framework\TestCase;

class OpenApiFactoryTest extends TestCase
{
    private PathsAppender|MockInterface $pathsAppender;
    private ComponentsAppender|MockInterface $componentsAppender;

    private OpenApiFactory $openApiFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->pathsAppender = Mock::create(PathsAppender::class);
        $this->componentsAppender = Mock::create(ComponentsAppender::class);

        $this->openApiFactory = new OpenApiFactory($this->pathsAppender, $this->componentsAppender);
    }

    /**
     * @test
     */
    public function shouldCreateOpenApiObjectAndCallAppenders()
    {
        //given
        $pathsAppenderCalled = false;
        Mock::when($this->pathsAppender)->handle(Mock::anyArgList())->thenAnswer(function (MethodCall $methodCall) use (&$pathsAppenderCalled) {
            $pathsAppenderCalled = true;
            return $this->callNextInterceptorInMock($methodCall);
        });

        $componentsAppenderCalled = false;
        Mock::when($this->componentsAppender)->handle(Mock::anyArgList())->thenAnswer(function (MethodCall $methodCall) use (&$componentsAppenderCalled) {
            $componentsAppenderCalled = true;
            return $this->callNextInterceptorInMock($methodCall);
        });

        //when
        $openApi = $this->openApiFactory->create('Title', 'Description', '0.0.1', 'https://example.com');

        //then
        $this->assertSame(OpenApiVersion::V_3_0_1, $openApi->getOpenapi());

        $info = $openApi->getInfo();
        $this->assertSame('Title', $info->getTitle());
        $this->assertSame('Description', $info->getDescription());
        $this->assertSame('0.0.1', $info->getVersion());

        $server = $openApi->getServers()[0];
        $this->assertSame('https://example.com', $server->getUrl());

        $this->assertTrue($pathsAppenderCalled);
        $this->assertTrue($componentsAppenderCalled);
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
