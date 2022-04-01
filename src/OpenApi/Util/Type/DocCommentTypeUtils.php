<?php

namespace Ouzo\OpenApi\Util\Type;

use Ouzo\Utilities\Arrays;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class DocCommentTypeUtils
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function getForReturn(ReflectionMethod $reflectionMethod, ?string $default = null): DocCommentType
    {
        $docComment = $reflectionMethod->getDocComment();
        $reflectionClass = $reflectionMethod->getDeclaringClass();
        return self::get($docComment, 'return', $reflectionClass, $default);
    }

    public static function getForProperty(ReflectionProperty $reflectionProperty, ?string $default = null): DocCommentType
    {
        $docComment = $reflectionProperty->getDocComment();
        $reflectionClass = $reflectionProperty->getDeclaringClass();
        return self::get($docComment, 'var', $reflectionClass, $default);
    }

    private static function get(bool|string $docComment, string $lookForTag, ReflectionClass $reflectionClass, ?string $default): DocCommentType
    {
        $emptyDocCommentType = new DocCommentType($default, null, false, false);

        if ($docComment !== false) {
            $docBlockFactory = DocBlockFactory::createInstance();
            $docBlock = $docBlockFactory->create($docComment);
            $vars = $docBlock->getTagsByName($lookForTag);
            /** @var Var_|null $var */
            $var = Arrays::getValue($vars, 0);

            return self::findType($var, $reflectionClass) ?: $emptyDocCommentType;
        }

        return $emptyDocCommentType;
    }

    private static function findType(?TagWithType $tag, ReflectionClass $reflectionClass): ?DocCommentType
    {
        if (is_null($tag)) {
            return null;
        }

        /** @var \Ouzo\OpenApi\Util\Type\Type $type */
        $type = $tag->getType();

        static $primitiveTypes = [
            Boolean::class,
            Integer::class,
            String_::class,
        ];

        $nullable = false;
        $array = false;

        if ($type instanceof Compound) {
            $nullable = $type->contains(new Null_());

            $iterator_to_array = iterator_to_array($type->getIterator());
            $filter = Arrays::filter($iterator_to_array, fn(Type $t) => $t::class !== Null_::class);
            $type = Arrays::first($filter);
        }

        if ($type instanceof Array_) {
            $array = true;

            $type = $type->getValueType();
        }

        // Fallback if type is mixed then return string type.
        if ($type instanceof Mixed_) {
            $type = new String_();
        }

        if (in_array($type::class, $primitiveTypes)) {
            return new DocCommentType($type->__toString(), null, $nullable, $array);
        }

        $classShortName = $type->__toString();
        return self::findFqdnClass($reflectionClass, $classShortName, $nullable, $array);
    }

    private static function findFqdnClass(ReflectionClass $reflectionClass, string $classShortName, bool $nullable, bool $array): DocCommentType
    {
        $fileName = $reflectionClass->getFileName();
        $content = file_get_contents($fileName);

        preg_match_all('/use (.*?);/', $content, $matches);
        foreach ($matches[1] as $match) {
            if (str_ends_with($match, $classShortName)) {
                return new DocCommentType(CompoundType::OBJECT, $match, $nullable, $array);
            }
        }

        $namespaceName = $reflectionClass->getNamespaceName();
        $fqdn = "{$namespaceName}{$classShortName}";
        return new DocCommentType(CompoundType::OBJECT, $fqdn, $nullable, $array);
    }
}
