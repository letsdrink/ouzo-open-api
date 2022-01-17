<?php

namespace Ouzo\OpenApi\Extractor;

use Ouzo\OpenApi\InternalDiscriminator;
use ReflectionClass;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

class DiscriminatorExtractor
{
    /** @return InternalDiscriminator[]|null */
    public function extract(?ReflectionClass $reflectionClass): ?array
    {
        if (is_null($reflectionClass)) {
            return null;
        }

        $reflectionAttributes = $reflectionClass->getAttributes(DiscriminatorMap::class);

        if (empty($reflectionAttributes)) {
            return null;
        }

        $reflectionAttribute = $reflectionAttributes[0];
        /** @var DiscriminatorMap $discriminatorMap */
        $discriminatorMap = $reflectionAttribute->newInstance();
        $mapping = [];
        foreach ($discriminatorMap->getMapping() as $k => $v) {
            $mapping[] = new InternalDiscriminator($k, new ReflectionClass($v), $discriminatorMap->getTypeProperty());
        }

        return $mapping;
    }
}
