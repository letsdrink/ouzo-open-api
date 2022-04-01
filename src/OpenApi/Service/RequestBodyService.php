<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Http\HttpMethod;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Model\Parameters\RequestBody;
use Ouzo\OpenApi\Util\Type\TypeUtils;
use ReflectionParameter;

class RequestBodyService
{
    #[Inject]
    public function __construct(private ContentService $contentService)
    {
    }

    public function create(ReflectionParameter $reflectionParameter, string $httpMethod): ?RequestBody
    {
        $reflectionType = $reflectionParameter->getType();

        $isObjectAndNotGetHttpMethod = !$reflectionType->isBuiltin() && $httpMethod !== HttpMethod::GET;
        if ($isObjectAndNotGetHttpMethod) {
            $content = $this->contentService->extracted($reflectionParameter);
            $requestBody = (new RequestBody())
                ->setContent($content);

            $type = TypeUtils::getForParameter($reflectionParameter);
            if (!$type->isNullable()) {
                $requestBody->setRequired(true);
            }

            return $requestBody;
        }

        return null;
    }
}
