<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\Extractor\PropertiesExtractor;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\OpenApi\InternalPathFactory;
use Ouzo\OpenApi\Model\ArraySchema;
use Ouzo\OpenApi\Model\Component;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\SimpleSchema;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Chain\Chain;
use PHPUnit\Framework\TestCase;

class ComponentsAppenderTest extends TestCase
{
    private CachedInternalPathProvider|MockInterface $cachedInternalPathProvider;

    private ComponentsAppender $componentsAppender;

    private Chain|MockInterface $chain;
    private InternalPathFactory $internalPathFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->cachedInternalPathProvider = Mock::create(CachedInternalPathProvider::class);
        $propertiesExtractor = new PropertiesExtractor();

        $this->componentsAppender = new ComponentsAppender($this->cachedInternalPathProvider, $propertiesExtractor);

        $this->chain = Mock::create(Chain::class);
        $this->internalPathFactory = new InternalPathFactory(new UriParametersExtractor(), new RequestBodyExtractor(), new ResponseExtractor());
    }

    /**
     * @test
     */
    public function shouldAddComponentsForRequestBodyObjects()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::POST, '/url1/:id', UsersController::class, 'update', true);

        Mock::when($this->cachedInternalPathProvider)->get()->thenReturn([
            $this->internalPathFactory->create($routeRule),
        ]);

        $openApi = new OpenApi();

        //when
        $this->componentsAppender->handle($openApi, $this->chain);

        //then
        $components = $openApi->getComponents();
        /** @var Component $component */
        $component = $components['schemas']['UserRequest'];
        $this->assertSame('object', $component->getType());
        Assert::thatArray($component->getProperties())
            ->containsOnly(
                (new SimpleSchema())->setType('string'),
                (new SimpleSchema())->setType('integer'),
                (new SimpleSchema())->setType('string')
            )
            ->keys()
            ->containsOnly('name', 'age', 'tagNames');

        Mock::verify($this->chain)->proceed($openApi);
    }

    /**
     * @test
     */
    public function shouldAddComponentsForResponsesObjects()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1', UsersController::class, 'info', true);

        Mock::when($this->cachedInternalPathProvider)->get()->thenReturn([
            $this->internalPathFactory->create($routeRule),
        ]);

        $openApi = new OpenApi();

        //when
        $this->componentsAppender->handle($openApi, $this->chain);

        //then
        $components = $openApi->getComponents();
        /** @var Component $component */
        $component = $components['schemas']['User'];
        $this->assertSame('object', $component->getType());
        Assert::thatArray($component->getProperties())
            ->containsOnly(
                (new ArraySchema())->setItems((new SimpleSchema())->setType('string')),
                (new ArraySchema())->setItems((new SimpleSchema())->setType('integer')),
                (new ArraySchema())->setItems((new RefSchema())->setRef('#/components/schemas/Tag')),
                (new ArraySchema())->setItems((new SimpleSchema())->setType('string'))
            )
            ->keys()
            ->containsOnly('withoutDocs', 'withPrimitive', 'withComplex', 'withEmptyTag');

        Mock::verify($this->chain)->proceed($openApi);
    }
}
