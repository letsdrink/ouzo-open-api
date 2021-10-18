<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

interface OpenApiAppender extends Interceptor
{
    /** @param OpenApi $param */
    public function handle(mixed $param, Chain $next): mixed;
}
