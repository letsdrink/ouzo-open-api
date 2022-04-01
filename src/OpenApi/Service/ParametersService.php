<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Http\HttpMethod;
use Ouzo\OpenApi\Model\ParameterIn;
use Ouzo\OpenApi\Model\Parameters\Parameter;
use Ouzo\OpenApi\Util\ReflectionUtils;
use Ouzo\OpenApi\Util\SchemaUtils;
use Ouzo\OpenApi\Util\Type\TypeUtils;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionParameter;

class ParametersService
{
    public function create(ReflectionParameter $reflectionParameter, array $pathParameterNames, string $httpMethod): ?array
    {
        $parameters = [];

        $parameterPathName = Arrays::getValue($pathParameterNames, $reflectionParameter->getPosition());
        if (!is_null($parameterPathName)) {
            $parameters[] = $this->getParameterForPath($reflectionParameter, $parameterPathName);
        } else {
            $parametersForQueryFromObject = $this->getParametersForQueryFromObject($reflectionParameter, $httpMethod);
            $parameters = array_merge($parameters, $parametersForQueryFromObject);
        }

        return empty($parameters) ? null : $parameters;
    }

    private function getParameterForPath(ReflectionParameter $reflectionParameter, string $parameterPathName): Parameter
    {
        $type = TypeUtils::getForParameter($reflectionParameter);
        $schema = SchemaUtils::create($type);

        return (new Parameter())
            ->setName($parameterPathName)
            ->setIn(ParameterIn::PATH)
            ->setRequired(true)
            ->setSchema($schema);
    }

    private function getParametersForQueryFromObject(ReflectionParameter $reflectionParameter, string $httpMethod): array
    {
        $reflectionType = $reflectionParameter->getType();

        $parameters = [];
        $isObjectAndGetHttpMethod = !$reflectionType->isBuiltin() && $httpMethod === HttpMethod::GET;
        if ($isObjectAndGetHttpMethod) {
            $reflectionProperties = ReflectionUtils::conditionallyGetProperties(new ReflectionClass($reflectionType->getName()), true);
            foreach ($reflectionProperties as $reflectionProperty) {
                $type = TypeUtils::getForProperty($reflectionProperty);
                $schema = SchemaUtils::create($type);
                $parameters[] = (new Parameter())
                    ->setName($reflectionProperty->getName())
                    ->setIn(ParameterIn::QUERY)
                    ->setSchema($schema);
            }
        }
        return $parameters;
    }
}
