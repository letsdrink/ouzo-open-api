<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\OpenApi\InternalPath;
use Ouzo\OpenApi\Model\Path;

class PathContext
{
    public function __construct(
        private Path $path,
        private InternalPath $internalPath
    )
    {
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function getInternalPath(): InternalPath
    {
        return $this->internalPath;
    }
}
