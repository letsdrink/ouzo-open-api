<?php

namespace Ouzo\Fixtures;

use Ouzo\Fixtures\AnotherNamespace\Category;

class User
{
    private array $withoutDocs;

    /** @var int[] */
    private array $withPrimitive;

    /** @var Tag[] */
    private array $withComplex;

    /** */
    private array $withEmptyTag;

    public function returnWithoutDocs(): array
    {
        return [];
    }

    /** @return string[] */
    public function returnWithPrimitive(): array
    {
        return [];
    }

    /** @return Tag[] */
    public function returnWithComplex(): array
    {
        return [];
    }

    /** @return Category[] */
    public function returnWithComplexFromAnotherNamespace(): array
    {
        return [];
    }
}
