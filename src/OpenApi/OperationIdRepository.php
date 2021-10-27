<?php

namespace Ouzo\OpenApi;

use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Strings;

class OperationIdRepository
{
    private array $operationIds = [];

    public function hasOperationId(string $operationId): bool
    {
        return in_array($operationId, $this->operationIds);
    }

    public function add(string $operationId): void
    {
        $this->operationIds[] = $operationId;
    }

    public function getLastOperationId(string $operationId): ?string
    {
        return FluentArray::from($this->operationIds)
            ->filter(fn(string $id) => Strings::startsWith($id, $operationId))
            ->sort(Comparator::reverse(Comparator::natural()))
            ->firstOr(null);
    }
}
