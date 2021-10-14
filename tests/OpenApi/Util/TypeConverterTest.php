<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Fixtures\User;
use Ouzo\OpenApi\Model\ArraySchema;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\SimpleSchema;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TypeConverterTest extends TestCase
{
    /**
     * @test
     * @dataProvider primitives
     */
    public function shouldConvertPrimitiveToOpenApiType(string $primitive, ?string $expected)
    {
        //when
        $openApiType = TypeConverter::convertPrimitiveToOpenApiType($primitive);

        //then
        $this->assertSame($expected, $openApiType);
    }

    public function primitives(): array
    {
        return [
            ['string', 'string'],
            ['mixed', 'string'],
            ['int', 'integer'],
            ['bool', 'boolean'],
            ['not-primitive', null],
        ];
    }

    /**
     * @test
     */
    public function shouldConvertPrimitiveTypeToSchema()
    {
        //given
        $typeWrapper = new PrimitiveTypeWrapper('string');

        //when
        /** @var SimpleSchema $schema */
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

        //then
        $this->assertInstanceOf(SimpleSchema::class, $schema);
        $this->assertSame('string', $schema->getType());
    }

    /**
     * @test
     */
    public function shouldConvertComplexTypeToSchema()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $typeWrapper = new ComplexTypeWrapper($reflectionClass);

        //when
        /** @var RefSchema $schema */
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

        //then
        $this->assertInstanceOf(RefSchema::class, $schema);
        $this->assertSame('#/components/schemas/User', $schema->getRef());
    }

    /**
     * @test
     */
    public function shouldConvertArrayPrimitiveTypeToSchema()
    {
        //given
        $typeWrapper = new ArrayTypeWrapperDecorator(new PrimitiveTypeWrapper('string'));

        //when
        /** @var ArraySchema $schema */
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

        //then
        $this->assertInstanceOf(ArraySchema::class, $schema);
        $this->assertSame('array', $schema->getType());

        /** @var SimpleSchema $subSchema */
        $subSchema = $schema->getItems();
        $this->assertInstanceOf(SimpleSchema::class, $subSchema);
        $this->assertSame('string', $subSchema->getType());
    }

    /**
     * @test
     */
    public function shouldConvertArrayComplexTypeToSchema()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $typeWrapper = new ArrayTypeWrapperDecorator(new ComplexTypeWrapper($reflectionClass));

        //when
        /** @var ArraySchema $schema */
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

        //then
        $this->assertInstanceOf(ArraySchema::class, $schema);
        $this->assertSame('array', $schema->getType());

        /** @var RefSchema $subSchema */
        $subSchema = $schema->getItems();
        $this->assertInstanceOf(RefSchema::class, $subSchema);
        $this->assertSame('#/components/schemas/User', $subSchema->getRef());
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenTypeWrapperIsNull()
    {
        //given
        $typeWrapper = null;

        //when
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

        //then
        $this->assertNull($schema);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenTypeInTypeWrapperIsNull()
    {
        //given
        $typeWrapper = new ComplexTypeWrapper(null);

        //when
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

        //then
        $this->assertNull($schema);
    }
}
