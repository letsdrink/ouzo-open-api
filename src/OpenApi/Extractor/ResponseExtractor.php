<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalResponse;
use Ouzo\OpenApi\TypeWrapper\ArrayTypeWrapperDecorator;
use Ouzo\OpenApi\TypeWrapper\ComplexType;
use Ouzo\OpenApi\TypeWrapper\ComplexTypeWrapper;
use Ouzo\OpenApi\TypeWrapper\PrimitiveType;
use Ouzo\OpenApi\TypeWrapper\PrimitiveTypeWrapper;
use Ouzo\OpenApi\Util\DocCommentTypeHelper;
use Ouzo\OpenApi\Util\TypeConverter;
use ReflectionClass;
use ReflectionMethod;

class ResponseExtractor
{
    public function extract(int $responseCode, ReflectionMethod $reflectionMethod): InternalResponse
    {
        if (!$reflectionMethod->hasReturnType()) {
            return new InternalResponse($responseCode);
        }

        $reflectionReturnType = $reflectionMethod->getReturnType();
        $name = $reflectionReturnType->getName();

        if ($name === 'void') {
            return new InternalResponse($responseCode);
        }

        if ($name === ComplexType::ARRAY) {
            $forReturn = DocCommentTypeHelper::getForReturn($reflectionMethod, PrimitiveType::STRING);
            $type = TypeConverter::convertPrimitiveToOpenApiType($forReturn);

            if (is_null($type)) {
                $reflectionClass = new ReflectionClass($forReturn);
                $typeWrapper = new ComplexTypeWrapper($reflectionClass);
            } else {
                $typeWrapper = new PrimitiveTypeWrapper($type);
            }

            $arrayTypeWrapperDecorator = new ArrayTypeWrapperDecorator($typeWrapper);
            return new InternalResponse($responseCode, $arrayTypeWrapperDecorator);
        }

        if ($reflectionReturnType->isBuiltin()) {
            $name = TypeConverter::convertPrimitiveToOpenApiType($name);
            $primitiveTypeWrapper = new PrimitiveTypeWrapper($name);
            return new InternalResponse($responseCode, $primitiveTypeWrapper);
        }

        $reflectionClass = new ReflectionClass($name);
        $complexTypeWrapper = new ComplexTypeWrapper($reflectionClass);

        return new InternalResponse($responseCode, $complexTypeWrapper);
    }
}
