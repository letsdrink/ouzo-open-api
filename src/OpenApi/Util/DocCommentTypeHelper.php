<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Utilities\Arrays;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class DocCommentTypeHelper
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function getForReturn(ReflectionMethod $reflectionMethod, ?string $default = null): ?string
    {
        $docComment = $reflectionMethod->getDocComment();

        if ($docComment !== false) {
            $reflectionClass = $reflectionMethod->getDeclaringClass();

            $docBlockFactory = DocBlockFactory::createInstance();
            $docBlock = $docBlockFactory->create($docComment);
            $returns = $docBlock->getTagsByName('return');
            /** @var Return_|null $return */
            $return = Arrays::getValue($returns, 0);

            return self::findType($return, $reflectionClass) ?: $default;
        }

        return $default;
    }

    public static function getForProperty(ReflectionProperty $reflectionProperty, ?string $default = null): ?string
    {
        $docComment = $reflectionProperty->getDocComment();

        if ($docComment !== false) {
            $reflectionClass = $reflectionProperty->getDeclaringClass();

            $docBlockFactory = DocBlockFactory::createInstance();
            $docBlock = $docBlockFactory->create($docComment);
            $vars = $docBlock->getTagsByName('var');
            /** @var Var_|null $var */
            $var = Arrays::getValue($vars, 0);

            return self::findType($var, $reflectionClass) ?: $default;
        }

        return $default;
    }

    private static function findType(?TagWithType $tag, ReflectionClass $reflectionClass): ?string
    {
        if (is_null($tag)) {
            return null;
        }

        /** @var Array_ $array */
        $array = $tag->getType();

        static $primitiveTypes = [
            Boolean::class,
            Integer::class,
            String_::class,
        ];

        $type = $array->getValueType();
        if (in_array($type::class, $primitiveTypes)) {
            return $type->__toString();
        }

        $classShortName = $type->__toString();
        return self::findFqdnClass($reflectionClass, $classShortName);
    }

    private static function findFqdnClass(ReflectionClass $reflectionClass, string $classShortName): string
    {
        $fileName = $reflectionClass->getFileName();
        $content = file_get_contents($fileName);

        preg_match_all('/use (.*?);/', $content, $matches);
        foreach ($matches[1] as $match) {
            if (str_ends_with($match, $classShortName)) {
                return $match;
            }
        }

        $namespaceName = $reflectionClass->getNamespaceName();
        return "{$namespaceName}${classShortName}";
    }
}
