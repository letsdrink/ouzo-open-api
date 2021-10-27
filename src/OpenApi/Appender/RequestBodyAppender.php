<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\RequestBody;
use Ouzo\OpenApi\Util\ComponentPathHelper;
use Ouzo\Utilities\Chain\Chain;

class RequestBodyAppender implements PathAppender
{
    /** @param PathContext $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $internalRequestBody = $param->getInternalPath()->getInternalRequestBody();

        $requestBody = null;
        if (!is_null($internalRequestBody)) {
            $reflectionClass = $internalRequestBody->getReflectionClass();

            $content = [
                $internalRequestBody->getMimeType() => [
                    'schema' => (new RefSchema())
                        ->setRef(ComponentPathHelper::getPathForReflectionClass($reflectionClass)),
                ],
            ];
            $requestBody = (new RequestBody())
                ->setContent($content)
                ->setRequired(true);
        }

        $param->getPath()->setRequestBody($requestBody);

        return $next->proceed($param);
    }
}
