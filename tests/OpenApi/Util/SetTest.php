<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    /**
     * @test
     */
    public function shouldAdd()
    {
        //given
        $set = new Set();

        //when
        $add = $set->add('item');

        //then
        $this->assertTrue($add);

        Assert::thatArray($set->all())
            ->containsOnly('item');
    }

    /**
     * @test
     */
    public function shouldNotAddWhenObjectExists()
    {
        //given
        $set = new Set();
        $set->add('item');

        //when
        $add = $set->add('item');

        //then
        $this->assertFalse($add);

        Assert::thatArray($set->all())
            ->containsOnly('item');
    }

    /**
     * @test
     */
    public function shouldAddAll()
    {
        //given
        $set = new Set();

        //when
        $set->addAll(['item1', 'item2', 'item3']);

        //then
        Assert::thatArray($set->all())
            ->containsOnly('item1', 'item2', 'item3');
    }

    /**
     * @test
     */
    public function shouldAddNotExistingWhenAddingAll()
    {
        //given
        $set = new Set();
        $set->add('item2');

        //when
        $set->addAll(['item1', 'item2', 'item3']);

        //then
        Assert::thatArray($set->all())
            ->containsOnly('item1', 'item2', 'item3');
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenSetContainsValue()
    {
        //given
        $class = new class {
            private string $property;

            public function setProperty(string $property): static
            {
                $this->property = $property;
                return $this;
            }
        };

        $obj = (new $class())->setProperty('value');

        $set = new Set();
        $set->add($obj);

        //when
        $contains = $set->contains($obj);

        //then
        $this->assertTrue($contains);
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenSetDoNotContainsValue()
    {
        //given
        $class = new class {
            private string $property;

            public function setProperty(string $property): static
            {
                $this->property = $property;
                return $this;
            }
        };

        $obj1 = (new $class())->setProperty('value');

        $set = new Set();
        $set->add($obj1);

        $obj2 = (new $class())->setProperty('another value');

        //when
        $contains = $set->contains($obj2);

        //then
        $this->assertFalse($contains);
    }

    /**
     * @test
     */
    public function shouldGetSize()
    {
        //given
        $set = new Set();
        $set->add('item1');
        $set->add('item2');

        //when
        $size = $set->size();

        //then
        $this->assertSame(2, $size);
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenIsNotEmpty()
    {
        //given
        $set = new Set();
        $set->add('item1');
        $set->add('item2');

        //when
        $empty = $set->isEmpty();

        //then
        $this->assertFalse($empty);
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenIsEmpty()
    {
        //given
        $set = new Set();

        //when
        $empty = $set->isEmpty();

        //then
        $this->assertTrue($empty);
    }

    /**
     * @test
     */
    public function shouldRemove()
    {
        //given
        $set = new Set();
        $set->add('item1');
        $set->add('item2');

        //when
        $remove = $set->remove('item2');

        //then
        $this->assertTrue($remove);

        Assert::thatArray($set->all())
            ->containsOnly('item1');
    }

    /**
     * @test
     */
    public function shouldNotRemoveRemoveWhenNotExists()
    {
        //given
        $set = new Set();
        $set->add('item1');
        $set->add('item2');

        //when
        $remove = $set->remove('not-exists');

        //then
        $this->assertFalse($remove);

        Assert::thatArray($set->all())
            ->containsOnly('item1', 'item2');
    }

    /**
     * @test
     */
    public function shouldClear()
    {
        //given
        $set = new Set();
        $set->add('item1');
        $set->add('item2');

        //when
        $set->clear();

        //then
        $this->assertEmpty($set->all());
    }

    /**
     * @test
     */
    public function shouldName()
    {
        //given

        //when
        //then
    }


}
