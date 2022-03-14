<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\Polymorphism\MessagesController;
use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\Extractor\ClassExtractor;
use Ouzo\OpenApi\Extractor\DiscriminatorExtractor;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\OpenApi\HiddenChecker;
use Ouzo\OpenApi\InternalPathFactory;
use Ouzo\OpenApi\Model\ArraySchema;
use Ouzo\OpenApi\Model\Component;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\SimpleSchema;
use Ouzo\OpenApi\OperationIdGenerator;
use Ouzo\OpenApi\OperationIdRepository;
use Ouzo\OpenApi\ReflectionClassesProvider;
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
        $classExtractor = new ClassExtractor(new DiscriminatorExtractor());

        $this->componentsAppender = new ComponentsAppender(new ReflectionClassesProvider($this->cachedInternalPathProvider), $classExtractor);

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
                (new ArraySchema())->setItems((new SimpleSchema())->setType('string')),
                (new ArraySchema())->setItems((new RefSchema())->setRef('#/components/schemas/Tag'))
            )
            ->keys()
            ->containsOnly('login', 'withoutDocs', 'withPrimitive', 'withComplex', 'withEmptyTag', 'nullableWithComplex');

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
        Assert::thatArray($directMessage->getRequired())
            ->containsOnly('userId');
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
    }

    /**
     * @test
     */
    public function shouldAppendAllClassesEvenIfTheyAreUsedInFields()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::POST, '/url', UsersController::class, 'multipleClassLevels', true);

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
            ->containsOnly('PropertiesExtractorClass', 'SubPropertiesExtractorClass');

        /** @var Component $propertiesExtractorClass */
        $propertiesExtractorClass = $schemas['PropertiesExtractorClass'];
        Assert::thatArray($propertiesExtractorClass->getProperties())
            ->keys()
            ->containsOnly('property1', 'property2', 'property3', 'property4', 'property5', 'parentProperty1', 'parentProperty2');

        /** @var Component $subPropertiesExtractorClass */
        $subPropertiesExtractorClass = $schemas['SubPropertiesExtractorClass'];
        Assert::thatArray($subPropertiesExtractorClass->getProperties())
            ->keys()
            ->containsOnly('subProperty1');
    }

    /**
     * @test
     */
    public function shouldExtractPolymorphicParametersFromWrapperClass()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', MessagesController::class, 'multipleMessages', true);

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
            ->containsOnly('Message', 'CommentMessage', 'DirectMessage', 'Messages');

        /** @var Component $message */
        $messages = $schemas['Messages'];
        $this->assertSame(OpenApiType::OBJECT, $messages->getType());
        $this->assertSame(OpenApiType::ARRAY, $messages->getProperties()['messages']['type']);
        Assert::thatArray($messages->getProperties()['messages']['items']['oneOf'])
            ->containsOnly(
                (new RefSchema())->setRef('#/components/schemas/CommentMessage'),
                (new RefSchema())->setRef('#/components/schemas/DirectMessage')
            );
        $this->assertNull($messages->getRequired());
        $this->assertNull($messages->getDiscriminator());

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
        Assert::thatArray($directMessage->getRequired())
            ->containsOnly('userId');
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
    }

    /**
     * @test
     */
    public function shouldExtractPolymorphicForSingleMessageWrappedByClass()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', MessagesController::class, 'wrappedSingleMessage', true);

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
            ->containsOnly('Message', 'CommentMessage', 'DirectMessage', 'WrappedMessage');

        /** @var Component $message */
        $wrappedMessage = $schemas['WrappedMessage'];
        $this->assertSame(OpenApiType::OBJECT, $wrappedMessage->getType());
        Assert::thatArray($wrappedMessage->getProperties()['message']['oneOf'])
            ->containsOnly(
                (new RefSchema())->setRef('#/components/schemas/CommentMessage'),
                (new RefSchema())->setRef('#/components/schemas/DirectMessage')
            );
        $this->assertNull($wrappedMessage->getRequired());
        $this->assertNull($wrappedMessage->getDiscriminator());
    }
}
