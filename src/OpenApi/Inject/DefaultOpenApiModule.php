<?php

namespace Ouzo\OpenApi\Inject;

use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\Loader\InjectModule;
use Ouzo\Injection\Scope;
use Ouzo\OpenApi\OperationIdRepository;

class DefaultOpenApiModule implements InjectModule
{
    /** @codeCoverageIgnore */
    public function configureBindings(InjectorConfig $config): void
    {
        $config->bind(OperationIdRepository::class)->in(Scope::SINGLETON);
    }
}
