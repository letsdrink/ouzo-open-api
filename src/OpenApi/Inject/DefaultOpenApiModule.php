<?php

namespace Ouzo\OpenApi\Inject;

use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\Loader\InjectModule;
use Ouzo\Injection\Scope;
use Ouzo\OpenApi\Customizer\OpenApiCustomizersRepository;
use Ouzo\OpenApi\Service\OperationId\OperationIdRepository;
use Ouzo\OpenApi\Service\SchemasRepository;

class DefaultOpenApiModule implements InjectModule
{
    /** @codeCoverageIgnore */
    public function configureBindings(InjectorConfig $config): void
    {
        $config->bind(OpenApiCustomizersRepository::class)->in(Scope::SINGLETON);
        $config->bind(OperationIdRepository::class)->in(Scope::SINGLETON);
        $config->bind(SchemasRepository::class)->in(Scope::SINGLETON);
    }
}
