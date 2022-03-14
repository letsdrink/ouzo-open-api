<?php

namespace Ouzo\Fixtures;

use Ouzo\Fixtures\AnotherNamespace\Category;
use Ouzo\OpenApi\Attribute\Schema;

class User
{
    #[Schema(required: true)]
    private string $login;

    private array $withoutDocs;

    /** @var int[] */
    private array $withPrimitive;

    /** @var Tag[] */
    private array $withComplex;

    /** */
    private array $withEmptyTag;

    /** @var Tag[]|null */
    private ?array $nullableWithComplex;

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
