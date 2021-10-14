<?php

namespace Ouzo\OpenApi;

use Ouzo\Http\HttpMethod;
use Ouzo\Http\HttpStatus;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Cache;
use PHPUnit\Framework\TestCase;

class CachedInternalPathProviderTest extends TestCase
{
    private RoutesProvider|MockInterface $routesProvider;
    private InternalPathFactory|MockInterface $internalPathFactory;

    private CachedInternalPathProvider $cachedInternalPathProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->routesProvider = Mock::create(RoutesProvider::class);
        $this->internalPathFactory = Mock::create(InternalPathFactory::class);

        $this->cachedInternalPathProvider = new CachedInternalPathProvider($this->routesProvider, $this->internalPathFactory);
    }

    /**
     * @test
     */
    public function shouldReturnMappedRouteRules()
    {
        //given
        Mock::when($this->routesProvider)->get()->thenReturn([
            new RouteRule(HttpMethod::GET, '/url1', 'controller1', 'action', true),
            new RouteRule(HttpMethod::POST, '/url2', 'controller2', 'action', true),
        ]);

        Mock::when($this->internalPathFactory)->create(Mock::anyArgList())->thenReturn(
            new InternalPath(new InternalPathDetails('/url1', 'tag1', 'summary1', 'operationId1', HttpMethod::GET), [], null, new InternalResponse(HttpStatus::OK)),
            new InternalPath(new InternalPathDetails('/url2', 'tag2', 'summary2', 'operationId2', HttpMethod::POST), [], null, new InternalResponse(HttpStatus::OK))
        );

        //when
        $this->cachedInternalPathProvider->get();

        //then
        Mock::verify($this->internalPathFactory)
            ->create(Mock::argThat()->extractExpression('getUri()', false)->equals('/url1'))
            ->create(Mock::argThat()->extractExpression('getUri()', false)->equals('/url2'));
    }

    /**
     * @test
     */
    public function shouldName()
    {
        //given
        Cache::clear();

        Mock::when($this->routesProvider)->get()->thenReturn([
            new RouteRule(HttpMethod::GET, '/url1', 'controller1', 'action', true),
            new RouteRule(HttpMethod::POST, '/url2', 'controller2', 'action', true),
        ]);

        Mock::when($this->internalPathFactory)->create(Mock::anyArgList())->thenReturn(
            new InternalPath(new InternalPathDetails('/url1', 'tag1', 'summary1', 'operationId1', HttpMethod::GET), [], null, new InternalResponse(HttpStatus::OK)),
            new InternalPath(new InternalPathDetails('/url2', 'tag2', 'summary2', 'operationId2', HttpMethod::POST), [], null, new InternalResponse(HttpStatus::OK))
        );

        $this->cachedInternalPathProvider->get();

        //when
        $this->cachedInternalPathProvider->get();

        //then
        Mock::verify($this->routesProvider)->receivedTimes(1)->get();
        Mock::verify($this->internalPathFactory)->receivedTimes(2)->create(Mock::anyArgList());
    }
}
