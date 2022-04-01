<?php

namespace Ouzo\Fixtures;

use Ouzo\OpenApi\Attributes\Hidden;

class SampleController
{
    public function scalarInReturn(): string
    {
        return '';
    }

    public function nullableScalarInReturn(): ?string
    {
        return '';
    }

    /** @return int[] */
    public function arrayOfScalarInReturn(): array
    {
        return [];
    }

    /** @return int[]|null */
    public function nullableArrayOfScalarInReturn(): ?array
    {
        return [];
    }

    public function objectInReturn(): Tag
    {
        return new Tag();
    }

    public function nullableObjectInReturn(): ?Tag
    {
        return new Tag();
    }

    /** @return Tag[] */
    public function arrayOfObjectInReturn(): array
    {
        return [];
    }

    /** @return Tag[]|null */
    public function nullableArrayOfObjectInReturn(): ?array
    {
        return [];
    }

    public function voidInReturn(): void
    {
    }

    public function withoutReturn()
    {
    }

    public function scalarInParameter(int $id): void
    {
    }

    public function objectInParameter(Tag $tag): void
    {
    }

    public function scalarAndObjectInParameter(int $id, Tag $tag): void
    {
    }

    public function objectWithAllTypesInReturn(): SampleClass
    {
        return new SampleClass();
    }

    public function arrayInReturnWithoutTypeInPhpDoc(): array
    {
        return [];
    }

    #[Hidden]
    public function hiddenMethod(): Tag
    {
        return new Tag();
    }
}
