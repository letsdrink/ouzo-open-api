<?php

namespace Ouzo\OpenApi\Model\Media;

class ComposedSchema extends Schema
{
    /** @var Schema[]|null */
    private ?array $allOf = null;

    /** @var Schema[]|null */
    private ?array $oneOf = null;

    /** @return Schema[]|null */
    public function getAllOf(): ?array
    {
        return $this->allOf;
    }

    /** @param Schema[]|null $allOf */
    public function setAllOf(?array $allOf): ComposedSchema
    {
        $this->allOf = $allOf;
        return $this;
    }

    /** @return Schema[]|null */
    public function getOneOf(): ?array
    {
        return $this->oneOf;
    }

    /** @param Schema[]|null $oneOf */
    public function setOneOf(?array $oneOf): ComposedSchema
    {
        $this->oneOf = $oneOf;
        return $this;
    }

    public function addOneOf(Schema $schema): ComposedSchema
    {
        $this->oneOf[] = $schema;
        return $this;
    }
}
