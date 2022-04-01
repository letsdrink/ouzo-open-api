<?php

namespace Ouzo\OpenApi\Customizer;

use Ouzo\OpenApi\Model\OpenApi;

interface OpenApiCustomizer
{
    public function customize(OpenApi $openApi): void;
}
