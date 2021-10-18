<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;

class Set
{
    private array $set = [];

    public function add(mixed $element): bool
    {
        if ($this->contains($element)) {
            return false;
        }

        $this->set[] = $element;
        return true;
    }

    public function addAll(array $elements): void
    {
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    public function contains(mixed $element): bool
    {
        return Arrays::contains($this->set, $element);
    }

    public function size(): int
    {
        return count($this->set);
    }

    public function isEmpty(): bool
    {
        return empty($this->set);
    }

    public function remove(mixed $element): bool
    {
        $filter = Arrays::filter($this->set, fn(mixed $item) => Objects::equal($element, $item));
        if (empty($filter)) {
            return false;
        }

        Arrays::removeNestedKey($this->set, array_keys($filter));
        return true;
    }

    public function clear(): void
    {
        $this->set = [];
    }

    public function all(): array
    {
        return $this->set;
    }
}
