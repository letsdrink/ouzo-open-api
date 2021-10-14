<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalRequestBody;
use Model\Utilities\Files\MimeType;
use Ouzo\Http\HttpMethod;
use ReflectionClass;
use ReflectionParameter;

class RequestBodyExtractor
{
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
            return new InternalRequestBody(MimeType::APPLICATION_JSON, $reflectionClass);
        }

        return null;
    }
}
