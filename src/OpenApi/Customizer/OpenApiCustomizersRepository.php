<?php

namespace Ouzo\OpenApi\Customizer;

class OpenApiCustomizersRepository
{
    /** @var OpenApiCustomizer[] */
    private array $openApiCustomizer = [];

    public function add(OpenApiCustomizer $openApiCustomizer): void
    {
        $this->openApiCustomizer[] = $openApiCustomizer;
    }

    /** @return OpenApiCustomizer[] */
    public function all(): array
    {
        return $this->openApiCustomizer;
    }
}
