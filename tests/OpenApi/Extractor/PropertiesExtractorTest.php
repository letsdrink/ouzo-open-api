<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\Fixtures\PropertiesExtractorClass;
use Ouzo\Fixtures\SubPropertiesExtractorClass;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PropertiesExtractorTest extends TestCase
{
    private PropertiesExtractor $propertiesExtractor;

    public function setUp(): void
    {
        parent::setUp();
        $this->propertiesExtractor = new PropertiesExtractor();
    }

    /**
     * @test
     */
    public function shouldExtract()
    {
        //given
        $reflectionClass = new ReflectionClass(PropertiesExtractorClass::class);

        //when
        $internalProperties = $this->propertiesExtractor->extract($reflectionClass);

        //then
        Assert::thatArray($internalProperties)
            ->hasSize(8)
            ->extracting('getName()', 'getReflectionDeclaringClass()', 'getTypeWrapper()')
            ->containsOnly(
                ['property1', new ReflectionClass(PropertiesExtractorClass::class), new PrimitiveTypeWrapper('string')],
                ['property2', new ReflectionClass(PropertiesExtractorClass::class), new PrimitiveTypeWrapper('string')],
                ['property3', new ReflectionClass(PropertiesExtractorClass::class), new ComplexTypeWrapper(new ReflectionClass(SubPropertiesExtractorClass::class))],
                ['property4', new ReflectionClass(PropertiesExtractorClass::class), new ArrayTypeWrapperDecorator(new ComplexTypeWrapper(new ReflectionClass(SubPropertiesExtractorClass::class)))],
                ['property5', new ReflectionClass(PropertiesExtractorClass::class), new ArrayTypeWrapperDecorator(new PrimitiveTypeWrapper('string'))],
                ['parentProperty1', new ReflectionClass(PropertiesExtractorClass::class), new PrimitiveTypeWrapper('string')],
                ['parentProperty2', new ReflectionClass(PropertiesExtractorClass::class), new ArrayTypeWrapperDecorator(new PrimitiveTypeWrapper('string'))],
                ['subProperty1', new ReflectionClass(SubPropertiesExtractorClass::class), new PrimitiveTypeWrapper('integer')],
            );
    }
}
