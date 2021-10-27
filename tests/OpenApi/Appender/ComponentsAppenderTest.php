<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\Polymorphism\MessagesController;
use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\ComponentClassWrapperProvider;
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
use Ouzo\OpenApi\TypeWrapper\OpenApiType;
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

        $this->componentsAppender = new ComponentsAppender(new ComponentClassWrapperProvider($this->cachedInternalPathProvider), $propertiesExtractor);

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
                (new SimpleSchema())->setType('string'),
                (new ArraySchema())->setItems((new SimpleSchema())->setType('string')),
                (new ArraySchema())->setItems((new SimpleSchema())->setType('integer')),
                (new ArraySchema())->setItems((new RefSchema())->setRef('#/components/schemas/Tag')),
                (new ArraySchema())->setItems((new SimpleSchema())->setType('string'))
            )
            ->keys()
            ->containsOnly('login', 'withoutDocs', 'withPrimitive', 'withComplex', 'withEmptyTag');

        Assert::thatArray($component->getRequired())
            ->containsOnly('login');

        Mock::verify($this->chain)->proceed($openApi);
    }

    /**
     * @test
     */
    public function shouldHandlePolymorphicObjects()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', MessagesController::class, 'singleMessage', true);

        Mock::when($this->cachedInternalPathProvider)->get()->thenReturn([
            $this->internalPathFactory->create($routeRule),
        ]);

        $openApi = new OpenApi();

        //when
        $this->componentsAppender->handle($openApi, $this->chain);

        //then
        $components = $openApi->getComponents();
        $schemas = $components['schemas'];
        Assert::thatArray($schemas)
            ->keys()
            ->containsOnly('Message', 'CommentMessage', 'DirectMessage');

        /** @var Component $message */
        $message = $schemas['Message'];
        $this->assertSame(OpenApiType::OBJECT, $message->getType());
        Assert::thatArray($message->getProperties())
            ->containsOnly((new SimpleSchema())->setType('string'))
            ->keys()
            ->containsOnly('messageType');
        Assert::thatArray($message->getRequired())
            ->containsOnly('messageType');
        $discriminator = $message->getDiscriminator();
        $this->assertSame('messageType', $discriminator->getPropertyName());
        Assert::thatArray($discriminator->getMapping())->containsKeyAndValue([
            'COMMENT' => '#/components/schemas/CommentMessage',
            'DIRECT' => '#/components/schemas/DirectMessage',
        ]);

        /** @var Component $commentMessage */
        $commentMessage = $schemas['CommentMessage'];
        $this->assertSame(OpenApiType::OBJECT, $commentMessage->getType());
        $this->assertNull($commentMessage->getRequired());
        $allOf = $commentMessage->getAllOf();
        $this->assertCount(2, $allOf);
        /** @var RefSchema $ref */
        $ref = $allOf[0];
        $this->assertSame('#/components/schemas/Message', $ref->getRef());
        /** @var Component $component */
        $component = $allOf[1];
        $this->assertSame(OpenApiType::OBJECT, $component->getType());
        Assert::thatArray($component->getProperties())
            ->containsOnly((new SimpleSchema())->setType('string'))
            ->keys()
            ->containsOnly('comment');

        /** @var Component $directMessage */
        $directMessage = $schemas['DirectMessage'];
        $this->assertSame(OpenApiType::OBJECT, $directMessage->getType());
        $this->assertNull($directMessage->getRequired());
        $allOf = $directMessage->getAllOf();
        $this->assertCount(2, $allOf);
        /** @var RefSchema $ref */
        $ref = $allOf[0];
        $this->assertSame('#/components/schemas/Message', $ref->getRef());
        /** @var Component $component */
        $component = $allOf[1];
        $this->assertSame(OpenApiType::OBJECT, $component->getType());
        Assert::thatArray($component->getProperties())
            ->containsOnly((new SimpleSchema())->setType('integer'), (new SimpleSchema())->setType('string'))
            ->keys()
            ->containsOnly('userId', 'body');

        $this->assertFalse(false);
    }
}
