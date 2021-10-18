<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Extractor\PropertiesExtractor;
use Ouzo\OpenApi\Model\Parameter;
use Ouzo\OpenApi\Model\ParameterIn;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Chain\Chain;
use ReflectionClass;

class ParametersAppender implements PathAppender
{
    #[Inject]
    public function __construct(private PropertiesExtractor $propertiesExtractor)
    {
    }

    /** @param PathContext $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $internalParameters = $param->getInternalPath()->getInternalParameters();

        $parameters = null;
        if (!is_null($internalParameters)) {
            foreach ($internalParameters as $internalParameter) {
                $typeWrapper = $internalParameter->getTypeWrapper();

                if ($typeWrapper->isPrimitive()) {
                    $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);

                    $parameters[] = (new Parameter())
                        ->setName($internalParameter->getName())
                        ->setIn(ParameterIn::PATH)
                        ->setDescription($internalParameter->getDescription())
                        ->setRequired(true)
                        ->setSchema($schema);
                } else {
                    /** @var ReflectionClass $reflectionClass */
                    $reflectionClass = $typeWrapper->get();
                    $internalProperties = $this->propertiesExtractor->extract($reflectionClass);
                    foreach ($internalProperties as $internalProperty) {
                        $schema = TypeConverter::convertTypeWrapperToSchema($internalProperty->getTypeWrapper());

                        $parameters[] = (new Parameter())
                            ->setName($internalProperty->getName())
                            ->setIn(ParameterIn::QUERY)
                            ->setRequired(true)
                            ->setSchema($schema);
                    }
                }
            }
        }

        $param->getPath()->setParameters($parameters);

        return $next->proceed($param);
    }
}
