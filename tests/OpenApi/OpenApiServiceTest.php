<?php

namespace Ouzo\OpenApi;

use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Fixtures\SampleController;
use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\Customizer\OpenApiCustomizer;
use Ouzo\OpenApi\Customizer\OpenApiCustomizersRepository;
use Ouzo\OpenApi\Model\Info\Info;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\Servers\Server;
use Ouzo\OpenApi\Service\ComponentsService;
use Ouzo\OpenApi\Service\ContentService;
use Ouzo\OpenApi\Service\HiddenChecker;
use Ouzo\OpenApi\Service\OperationId\OperationIdGenerator;
use Ouzo\OpenApi\Service\OperationId\OperationIdRepository;
use Ouzo\OpenApi\Service\OperationService;
use Ouzo\OpenApi\Service\ParametersService;
use Ouzo\OpenApi\Service\PathsService;
use Ouzo\OpenApi\Service\RequestBodyService;
use Ouzo\OpenApi\Service\SchemasRepository;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class OpenApiServiceTest extends TestCase
{
    use WithRouteRulesProvider;

    /**
     * @test
     */
    public function shouldGenerateJsonWithOpenApi()
    {
        //given
        $routeRules = [
            new RouteRule(HttpMethod::GET, '/users_1', SampleController::class, 'scalarInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_2', SampleController::class, 'nullableScalarInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_3', SampleController::class, 'arrayOfScalarInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_4', SampleController::class, 'nullableArrayOfScalarInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_5', SampleController::class, 'objectInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_6', SampleController::class, 'nullableObjectInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_7', SampleController::class, 'arrayOfObjectInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_8', SampleController::class, 'nullableArrayOfObjectInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_9', SampleController::class, 'voidInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_10', SampleController::class, 'withoutReturn', true),
            new RouteRule(HttpMethod::GET, '/users_11/:id', SampleController::class, 'scalarInParameter', true),
            new RouteRule(HttpMethod::GET, '/users_12', SampleController::class, 'objectInParameter', true),
            new RouteRule(HttpMethod::POST, '/users_13', SampleController::class, 'objectInParameter', true),
            new RouteRule(HttpMethod::GET, '/users_14/:id', SampleController::class, 'scalarAndObjectInParameter', true),
            new RouteRule(HttpMethod::POST, '/users_15/:id', SampleController::class, 'scalarAndObjectInParameter', true),
            new RouteRule(HttpMethod::GET, '/users_16', SampleController::class, 'objectWithAllTypesInReturn', true),
            new RouteRule(HttpMethod::GET, '/users_17', SampleController::class, 'arrayInReturnWithoutTypeInPhpDoc', true),
        ];

        $schemasRepository = new SchemasRepository();

        $routesProvider = $this->getRouteRulesProvider($routeRules);
        $hiddenChecker = new HiddenChecker();
        $operationIdGenerator = new OperationIdGenerator(new OperationIdRepository());
        $parametersService = new ParametersService();
        $contentService = new ContentService($schemasRepository);
        $requestBodyService = new RequestBodyService($contentService);
        $operationService = new OperationService($operationIdGenerator, $parametersService, $requestBodyService, $contentService);

        $pathsService = new PathsService($routesProvider, $hiddenChecker, $operationService);
        $componentsService = new ComponentsService($schemasRepository);

        $openApiCustomizersRepository = new OpenApiCustomizersRepository();
        $openApiCustomizersRepository->add($this->getOpenApiCustomizer());

        $openApiService = new OpenApiService($pathsService, $componentsService, $openApiCustomizersRepository);

        //when
        $openApi = $openApiService->create();

        //then
        $path = Path::join(__DIR__, 'expected-openapi.json');
        $serialized = $this->serialize($openApi);
        $this->assertJsonStringEqualsJsonFile($path, $serialized);
    }

    private function getOpenApiCustomizer(): OpenApiCustomizer
    {
        return new class implements OpenApiCustomizer {
            public function customize(OpenApi $openApi): void
            {
                $openApi
                    ->setInfo((new Info())
                        ->setTitle('title')
                        ->setDescription('description')
                        ->setVersion('8.3.1')
                    )
                    ->setServers([
                        (new Server())
                            ->setUrl('https://example.com'),
                    ]);
            }
        };
    }

    private function serialize(OpenApi $openApi): string
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $normalizers = [new JsonSerializableNormalizer(), new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter)];
        $encoders = [new XmlEncoder(), new YamlEncoder(), new JsonEncoder()];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($openApi, 'json', [
            YamlEncoder::YAML_INLINE => 11,
            YamlEncoder::YAML_FLAGS => Yaml::DUMP_OBJECT_AS_MAP,
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        ]);
    }
}
