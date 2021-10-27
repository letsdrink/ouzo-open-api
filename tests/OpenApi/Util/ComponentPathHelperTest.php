<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Fixtures\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ComponentPathHelperTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetPathForReflectionClass()
    {
        //given
        $reflectionClass = new ReflectionClass(User::class);

        //when
        $path = ComponentPathHelper::getPathForReflectionClass($reflectionClass);

        //then
        $this->assertSame('#/components/schemas/User', $path);
    }
}
