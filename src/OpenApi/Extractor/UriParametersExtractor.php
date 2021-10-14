<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalParameter;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Http\HttpMethod;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Strings;
use ReflectionClass;
use ReflectionParameter;

class UriParametersExtractor
{
    /**
     * @param ReflectionParameter[] $reflectionParameters
     * @return InternalParameter[]|null
     */
    public function extract(string $uri, string $httpMethod, array $reflectionParameters): ?array
    {
        $pathParameterNames = $this->getPathParameterNames($uri);

        if (empty($pathParameterNames) && $httpMethod !== HttpMethod::GET) {
            return null;
        }

        $parameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $reflectionType = $reflectionParameter->getType();
            $type = TypeConverter::convertPrimitiveToSwaggerType($reflectionType);

            if (is_null($type) && $httpMethod === HttpMethod::GET) {
                $name = $reflectionParameter->getName();
                $description = $this->generateDescription($name);
                $reflectionClass = new ReflectionClass($reflectionType->getName());
                $typeWrapper = new ComplexTypeWrapper($reflectionClass);
                $parameters[] = new InternalParameter($name, $description, $typeWrapper);
            } else {
                $name = $pathParameterNames[$reflectionParameter->getPosition()] ?? null;
                if (!is_null($name)) {
                    $description = $this->generateDescription($name);
                    $typeWrapper = new PrimitiveTypeWrapper($type);
                    $parameters[] = new InternalParameter($name, $description, $typeWrapper);
                }
            }
        }

        return $parameters ?: null;
    }

    private function getPathParameterNames(string $uri): array
    {
        preg_match_all('/\\{(.*?)\\}/', $uri, $pathParameterNames);
        return FluentArray::from($pathParameterNames)
            ->flatten()
            ->filterNotBlank()
            ->filter(fn(string $param) => !Strings::contains($param, '{'))
            ->values()
            ->toArray();
    }

    private function generateDescription(mixed $name): string
    {
        return str_replace('_', ' ', Strings::camelCaseToUnderscore($name));
    }
}
