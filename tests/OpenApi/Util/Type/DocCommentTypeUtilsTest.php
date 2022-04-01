<?php

namespace Ouzo\OpenApi\Util\Type;

use Ouzo\Fixtures\SampleClass;
use Ouzo\Fixtures\SampleController;
use Ouzo\Fixtures\Tag;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DocCommentTypeUtilsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldGetNullWhenReturnIsArrayAndIsWithoutDocComment()
    {
        //given
        $reflectionClass = new ReflectionClass(SampleController::class);
        $reflectionMethod = $reflectionClass->getMethod('arrayInReturnWithoutTypeInPhpDoc');

        //when
        $forReturn = DocCommentTypeUtils::getForReturn($reflectionMethod);

        //then
        $this->assertNull($forReturn->getType());
        $this->assertNull($forReturn->getClass());
        $this->assertFalse($forReturn->isNullable());
        $this->assertFalse($forReturn->isArray());
    }

    /**
     * @test
     */
    public function shouldGetArrayOfScalarTypesForReturnWithDocComment()
    {
        //given
        $reflectionClass = new ReflectionClass(SampleController::class);
        $reflectionMethod = $reflectionClass->getMethod('arrayOfScalarInReturn');

        //when
        $forReturn = DocCommentTypeUtils::getForReturn($reflectionMethod);

        //then
        $this->assertSame('int', $forReturn->getType());
        $this->assertNull($forReturn->getClass());
        $this->assertFalse($forReturn->isNullable());
        $this->assertTrue($forReturn->isArray());
    }

    /**
     * @test
     */
    public function shouldGetArrayOfObjectTypesForReturnWithDocComment()
    {
        //given
        $reflectionClass = new ReflectionClass(SampleController::class);
        $reflectionMethod = $reflectionClass->getMethod('arrayOfObjectInReturn');

        //when
        $forReturn = DocCommentTypeUtils::getForReturn($reflectionMethod);

        //then
        $this->assertSame('object', $forReturn->getType());
        $this->assertSame(Tag::class, $forReturn->getClass());
        $this->assertFalse($forReturn->isNullable());
        $this->assertTrue($forReturn->isArray());
    }

    /**
     * @test
     */
    public function shouldGetArrayOfObjectTypesForPropertyWithDocComment()
    {
        //given
        $reflectionClass = new ReflectionClass(SampleClass::class);
        $reflectionProperty = $reflectionClass->getProperty('arrayOfObjects');

        //when
        $forProperty = DocCommentTypeUtils::getForProperty($reflectionProperty);

        //then
        $this->assertSame('object', $forProperty->getType());
        $this->assertSame(Tag::class, $forProperty->getClass());
        $this->assertFalse($forProperty->isNullable());
        $this->assertTrue($forProperty->isArray());
    }

    /**
     * @test
     */
    public function shouldGetNullWhenDocsExistsButDoNotHaveSetTags()
    {
        //given
        $reflectionClass = new ReflectionClass(SampleClass::class);
        $reflectionProperty = $reflectionClass->getProperty('arrayWithoutDocType');

        //when
        $forProperty = DocCommentTypeUtils::getForProperty($reflectionProperty);

        //then
        $this->assertNull($forProperty->getType());
        $this->assertNull($forProperty->getClass());
        $this->assertFalse($forProperty->isNullable());
        $this->assertFalse($forProperty->isArray());
    }
}
