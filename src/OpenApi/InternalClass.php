<?php

namespace Ouzo\OpenApi;

class InternalClass
{
    /** @param InternalProperty[] $internalProperties */
    public function __construct(
        private ComponentClassWrapper $componentClassWrapper,
        private array $internalProperties
    )
    {
    }

    public function getComponentClassWrapper(): ComponentClassWrapper
    {
        return $this->componentClassWrapper;
    }

    /** @return InternalProperty[] */
    public function getInternalProperties(): array
    {
        return $this->internalProperties;
    }
}
