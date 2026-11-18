<?php

namespace Ouzo\OpenApi\Util\Type;

use Ouzo\Fixtures\ChildClass;
use Ouzo\Fixtures\SampleClass;
use Ouzo\Fixtures\SampleController;
use Ouzo\Fixtures\Tag;
use Ouzo\Fixtures\Traits\ClassOverridingTraitMembers;
use Ouzo\Fixtures\Traits\ClassWithTraits;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DocCommentTypeUtilsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function shouldGetNullWhenReturnIsArrayAndIsWithoutDocComment(): void
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

    #[Test]
    public function shouldGetArrayOfScalarTypesForReturnWithDocComment(): void
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

    #[Test]
    public function shouldGetArrayOfObjectTypesForReturnWithDocComment(): void
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

    #[Test]
    public function shouldGetArrayOfObjectTypesForPropertyWithDocComment(): void
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

    #[Test]
    public function shouldGetNullWhenDocsExistsButDoNotHaveSetTags(): void
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

    #[Test]
    public function shouldUseFullyQualifiedClassNameFromDocComment(): void
    {
        //given
        $reflectionClass = new ReflectionClass(SampleClass::class);
        $reflectionProperty = $reflectionClass->getProperty('arrayOfObjectsWithFullyQualifiedNameInPhpDoc');

        //when
        $forProperty = DocCommentTypeUtils::getForProperty($reflectionProperty);

        //then
        $this->assertSame('object', $forProperty->getType());
        $this->assertSame(Tag::class, $forProperty->getClass());
        $this->assertFalse($forProperty->isNullable());
        $this->assertTrue($forProperty->isArray());
    }

    #[Test]
    public function shouldResolveClassAgainstImportsOfTraitDeclaringProperty(): void
    {
        //given
        $reflectionClass = new ReflectionClass(ClassWithTraits::class);
        $reflectionProperty = $reflectionClass->getProperty('children');

        //when
        $forProperty = DocCommentTypeUtils::getForProperty($reflectionProperty);

        //then
        $this->assertSame('object', $forProperty->getType());
        $this->assertSame(ChildClass::class, $forProperty->getClass());
        $this->assertFalse($forProperty->isNullable());
        $this->assertTrue($forProperty->isArray());
    }

    #[Test]
    public function shouldResolveClassAgainstImportsOfNestedTraitDeclaringProperty(): void
    {
        //given
        $reflectionClass = new ReflectionClass(ClassWithTraits::class);
        $reflectionProperty = $reflectionClass->getProperty('tags');

        //when
        $forProperty = DocCommentTypeUtils::getForProperty($reflectionProperty);

        //then
        $this->assertSame('object', $forProperty->getType());
        $this->assertSame(Tag::class, $forProperty->getClass());
        $this->assertTrue($forProperty->isNullable());
        $this->assertTrue($forProperty->isArray());
    }

    #[Test]
    public function shouldResolveClassAgainstImportsOfTraitDeclaringMethod(): void
    {
        //given
        $reflectionClass = new ReflectionClass(ClassWithTraits::class);
        $reflectionMethod = $reflectionClass->getMethod('getTags');

        //when
        $forReturn = DocCommentTypeUtils::getForReturn($reflectionMethod);

        //then
        $this->assertSame('object', $forReturn->getType());
        $this->assertSame(Tag::class, $forReturn->getClass());
        $this->assertFalse($forReturn->isNullable());
        $this->assertTrue($forReturn->isArray());
    }

    #[Test]
    public function shouldResolveClassAgainstImportsOfClassRedeclaringTraitProperty(): void
    {
        //given
        $reflectionClass = new ReflectionClass(ClassOverridingTraitMembers::class);
        $reflectionProperty = $reflectionClass->getProperty('tags');

        //when
        $forProperty = DocCommentTypeUtils::getForProperty($reflectionProperty);

        //then
        $this->assertSame('object', $forProperty->getType());
        $this->assertSame(ChildClass::class, $forProperty->getClass());
        $this->assertTrue($forProperty->isNullable());
        $this->assertTrue($forProperty->isArray());
    }

    #[Test]
    public function shouldResolveClassAgainstImportsOfClassOverridingTraitMethod(): void
    {
        //given
        $reflectionClass = new ReflectionClass(ClassOverridingTraitMembers::class);
        $reflectionMethod = $reflectionClass->getMethod('getTags');

        //when
        $forReturn = DocCommentTypeUtils::getForReturn($reflectionMethod);

        //then
        $this->assertSame('object', $forReturn->getType());
        $this->assertSame(ChildClass::class, $forReturn->getClass());
        $this->assertFalse($forReturn->isNullable());
        $this->assertTrue($forReturn->isArray());
    }
}
