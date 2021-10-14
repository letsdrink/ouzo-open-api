<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalResponse;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\Util\DocCommentTypeHelper;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use ReflectionClass;
use ReflectionMethod;

class ResponseExtractor
{
    public function extract(RouteRule $routeRule, ReflectionMethod $reflectionMethod): InternalResponse
    {
        $responseCode = Arrays::getValue($routeRule->getOptions(), 'code', 200);

        if (!$reflectionMethod->hasReturnType()) {
            return new InternalResponse($responseCode);
        }

        $reflectionReturnType = $reflectionMethod->getReturnType();
        $name = $reflectionReturnType->getName();

        if ($name === 'void') {
            return new InternalResponse($responseCode);
        }

        if ($name === 'array') {
            $reflectionClass = $this->getClassForReturnArray($reflectionMethod);
            $complexTypeWrapper = new ComplexTypeWrapper($reflectionClass);
            $arrayTypeWrapperDecorator = new ArrayTypeWrapperDecorator($complexTypeWrapper);
            return new InternalResponse($responseCode, $arrayTypeWrapperDecorator);
        }

        if ($reflectionReturnType->isBuiltin()) {
            $name = TypeConverter::convertPrimitiveToSwaggerType($name);
            $primitiveTypeWrapper = new PrimitiveTypeWrapper($name);
            return new InternalResponse($responseCode, $primitiveTypeWrapper);
        }

        $reflectionClass = new ReflectionClass($name);
        $complexTypeWrapper = new ComplexTypeWrapper($reflectionClass);

        return new InternalResponse($responseCode, $complexTypeWrapper);
    }

    private function getClassForReturnArray(ReflectionMethod $reflectionMethod): ?ReflectionClass
    {
        $forReturn = DocCommentTypeHelper::getForReturn($reflectionMethod);
        return is_null($forReturn) ? null : new ReflectionClass($forReturn);
    }
}
