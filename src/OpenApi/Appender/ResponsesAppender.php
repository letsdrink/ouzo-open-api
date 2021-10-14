<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\OpenApi\Model\Response;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class ResponsesAppender implements Interceptor
{
    /** @param PathContext $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $internalResponse = $param->getInternalPath()->getInternalResponse();

        $typeWrapper = $internalResponse->getTypeWrapper();
        $response = (new Response())
            ->setDescription('success');
        $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);
        if (!is_null($schema)) {
            $response->setContent([
                'application/json' => [
                    'schema' => $schema,
                ],
            ]);
        }

        $param->getPath()->setResponses([$internalResponse->getResponseCode() => $response]);

        return $next->proceed($param);
    }
}
