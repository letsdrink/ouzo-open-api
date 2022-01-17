<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\Attribute\Schema;
use Ouzo\OpenApi\TypeWrapper\TypeWrapper;

class InternalProperty
{
    /** @param InternalDiscriminator[]|null $internalDiscriminator */
    public function __construct(
        private string $name,
        private TypeWrapper $typeWrapper,
        private ?Schema $schema,
        private ?array $internalDiscriminator = null
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTypeWrapper(): TypeWrapper
    {
        return $this->typeWrapper;
    }

    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    /** @return InternalDiscriminator[]|null */
    public function getInternalDiscriminator(): ?array
    {
        return $this->internalDiscriminator;
    }
}
