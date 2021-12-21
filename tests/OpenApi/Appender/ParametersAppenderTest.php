<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Fixtures\UsersController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\Extractor\ClassExtractor;
use Ouzo\OpenApi\Extractor\RequestBodyExtractor;
use Ouzo\OpenApi\Extractor\ResponseExtractor;
use Ouzo\OpenApi\Extractor\UriParametersExtractor;
use Ouzo\OpenApi\HiddenChecker;
use Ouzo\OpenApi\InternalPathFactory;
use Ouzo\OpenApi\Model\ParameterIn;
use Ouzo\OpenApi\Model\Path;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\SimpleSchema;
use Ouzo\OpenApi\OperationIdGenerator;
use Ouzo\OpenApi\OperationIdRepository;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Chain\Chain;
use PHPUnit\Framework\TestCase;

class ParametersAppenderTest extends TestCase
{
    private ParametersAppender $parametersAppender;

    private Chain|MockInterface $chain;
    private InternalPathFactory $internalPathFactory;

    public function setUp(): void
    {
        parent::setUp();
        $classExtractor = new ClassExtractor();

        $this->parametersAppender = new ParametersAppender($classExtractor);

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
    public function shouldAddParametersFromPath()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::POST, '/url1/:id', UsersController::class, 'update', true);

        $path = new Path();
        $pathContext = new PathContext($path, $this->internalPathFactory->create($routeRule));

        //when
        $this->parametersAppender->handle($pathContext, $this->chain);

        //then
        Assert::thatArray($path->getParameters())
            ->extracting('getName()', 'getIn()', 'getDescription()', 'isRequired()', 'getSchema()')
            ->containsOnly(
                ['id', ParameterIn::PATH, 'id', true, (new SimpleSchema())->setType('integer')]
            );

        Mock::verify($this->chain)->proceed($pathContext);
    }

    /**
     * @test
     */
    public function shouldAddParametersFromQuery()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url1', UsersController::class, 'filter', true);

        $path = new Path();
        $pathContext = new PathContext($path, $this->internalPathFactory->create($routeRule));

        //when
        $this->parametersAppender->handle($pathContext, $this->chain);

        //then
        Assert::thatArray($path->getParameters())
            ->extracting('getName()', 'getIn()', 'getDescription()', 'isRequired()', 'getSchema()')
            ->containsOnly(
                ['name', ParameterIn::QUERY, null, true, (new SimpleSchema())->setType('string')],
                ['age', ParameterIn::QUERY, null, false, (new SimpleSchema())->setType('integer')],
                ['tag', ParameterIn::QUERY, null, false, (new RefSchema())->setRef('#/components/schemas/Tag')],
                ['tagName', ParameterIn::QUERY, null, false, (new SimpleSchema())->setType('string')]
            );

        Mock::verify($this->chain)->proceed($pathContext);
    }
}
