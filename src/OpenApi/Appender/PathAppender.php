<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

interface PathAppender extends Interceptor
{
    /** @param PathContext $param */
    public function handle(mixed $param, Chain $next): mixed;
}
