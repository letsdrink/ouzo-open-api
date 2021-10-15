<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\InternalRequestBody;
use ReflectionClass;
use ReflectionParameter;

class RequestBodyExtractor
{
    private const MIME_TYPE = 'application/json';

    /** @param ReflectionParameter[] $reflectionParameters */
    public function extract(array $reflectionParameters, string $httpMethod): ?InternalRequestBody
    {
        if ($httpMethod === HttpMethod::GET) {
            return null;
        }

        foreach ($reflectionParameters as $reflectionParameter) {
            $reflectionType = $reflectionParameter->getType();

            if ($reflectionType->isBuiltin()) {
                continue;
            }

            $class = $reflectionType->getName();
            $reflectionClass = new ReflectionClass($class);
            return new InternalRequestBody(self::MIME_TYPE, $reflectionClass);
        }

        return null;
    }
}
