<?php
declare(strict_types=1);

namespace Ouzo\OpenApi\Service\OperationId;

use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Strings;

class OperationIdRepository
{
    /** @var string[] */
    private array $operationIds = [];

    public function hasOperationId(string $operationId): bool
    {
        return in_array($operationId, $this->operationIds);
    }

    public function add(string $operationId): void
    {
        $this->operationIds[] = $operationId;
    }

    public function getLastOperationId(string $operationId): string
    {
        return FluentArray::from($this->operationIds)
            ->filter(fn(string $id): bool => preg_match("/{$operationId}_\d+/", $id) === 1)
            ->sort(Comparator::reverse(Comparator::natural()))
            ->firstOr(Strings::EMPTY);
    }

    public function all(): array
    {
        return $this->operationIds;
    }
}
