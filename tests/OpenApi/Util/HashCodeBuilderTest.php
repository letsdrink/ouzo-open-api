<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Fixtures\HashCodeClass;
use PHPUnit\Framework\TestCase;
use stdClass;

class HashCodeBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCalculateForNull()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = null;

        //then
        $this->assertSame(629, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForBool()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = false;

        //then
        $this->assertSame(630, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForInt()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = 10;

        //then
        $this->assertSame(639, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForFloat()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = 12.0123;

        //then
        $this->assertSame(13478, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForString()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = 'string';

        //then
        $this->assertSame(1292, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForArray()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = [1, 2];

        //then
        $this->assertSame(23312, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForArrayWithMixedTypes()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = ['string', 2];

        //then
        $this->assertSame(47806, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForObject()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        $stdClass = new stdClass();
        $stdClass->prop1 = 'string';
        $stdClass->prop2 = 2;

        //when
        $hashCodeClass->mixed = $stdClass;

        //then
        $this->assertSame(47806, $hashCodeClass->hashCode());
    }

    /**
     * @test
     */
    public function shouldCalculateForClass()
    {
        //given
        $hashCodeClass = new HashCodeClass();

        //when
        $hashCodeClass->mixed = new class {
            private string $prop1 = 'string';
            private int $prop2 = 2;
        };

        //then
        $this->assertSame(47806, $hashCodeClass->hashCode());
    }
}
