<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Fixtures\AnotherNamespace\Category;
use Ouzo\Fixtures\Tag;
use Ouzo\Fixtures\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DocCommentTypeHelperTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetNullWhenReturnIsWithoutDocComment()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionMethod = $reflectionClass->getMethod('returnWithoutDocs');

        //when
        $forReturn = DocCommentTypeHelper::getForReturn($reflectionMethod);

        //then
        $this->assertNull($forReturn);
    }

    /**
     * @test
     */
    public function shouldGetPrimitiveTypeForReturnWhichIsDeclaredInDocs()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionMethod = $reflectionClass->getMethod('returnWithPrimitive');

        //when
        $forReturn = DocCommentTypeHelper::getForReturn($reflectionMethod);

        //then
        $this->assertSame('string', $forReturn);
    }

    /**
     * @test
     */
    public function shouldGetComplexTypeForReturnWhichIsDeclaredInDocs()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionMethod = $reflectionClass->getMethod('returnWithComplex');

        //when
        $forReturn = DocCommentTypeHelper::getForReturn($reflectionMethod);

        //then
        $this->assertSame(Tag::class, $forReturn);
    }

    /**
     * @test
     */
    public function shouldGetComplexTypeFromAnotherNamespaceForReturnWhichIsDeclaredInDocs()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionMethod = $reflectionClass->getMethod('returnWithComplexFromAnotherNamespace');

        //when
        $forReturn = DocCommentTypeHelper::getForReturn($reflectionMethod);

        //then
        $this->assertSame(Category::class, $forReturn);
    }

    /**
     * @test
     */
    public function shouldGetNullWhenPropertyDoNotHaveDocs()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('withoutDocs');

        //when
        $forProperty = DocCommentTypeHelper::getForProperty($reflectionProperty);

        //then
        $this->assertNull($forProperty);
    }

    /**
     * @test
     */
    public function shouldGetPrimitiveForProperty()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('withPrimitive');

        //when
        $forProperty = DocCommentTypeHelper::getForProperty($reflectionProperty);

        //then
        $this->assertSame('int', $forProperty);
    }

    /**
     * @test
     */
    public function shouldGetComplexForProperty()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('withComplex');

        //when
        $forProperty = DocCommentTypeHelper::getForProperty($reflectionProperty);

        //then
        $this->assertSame(Tag::class, $forProperty);
    }

    /**
     * @test
     */
    public function shouldGetNullWhenDocsExistsButDoNotHaveSetTags()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('withEmptyTag');

        //when
        $forProperty = DocCommentTypeHelper::getForProperty($reflectionProperty);

        //then
        $this->assertNull($forProperty);
    }
}
