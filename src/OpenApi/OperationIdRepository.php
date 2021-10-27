<?php

namespace Ouzo\OpenApi;

use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;

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
            ->filter(fn(string $id) => preg_match("/{$operationId}_\d+/", $id) === 1)
            ->sort(Comparator::reverse(Comparator::natural()))
            ->firstOr(null);
    }

    public function all(): array
    {
        return $this->operationIds;
    }
}
