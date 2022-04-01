<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Model\Components;

class ComponentsService
{
    #[Inject]
    public function __construct(private SchemasRepository $schemasRepository)
    {
    }

    public function create(): ?Components
    {
        if ($this->schemasRepository->isEmpty()) {
            return null;
        }

        $all = $this->schemasRepository->all();
        return (new Components())
            ->setSchemas($all);
    }
}
