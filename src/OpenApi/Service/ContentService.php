<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Model\Media\Content;
use Ouzo\OpenApi\Model\Media\MediaType;
use Ouzo\OpenApi\Util\SchemaUtils;
use Ouzo\OpenApi\Util\Type\TypeUtils;
use ReflectionMethod;
use ReflectionParameter;

class ContentService
{
    #[Inject]
    public function __construct(private SchemasRepository $schemasRepository)
    {
    }

    public function create(ReflectionMethod $reflectionMethod): ?Content
    {
        $content = null;

        $type = TypeUtils::getForReturnType($reflectionMethod);
        $schema = SchemaUtils::create($type);

        $this->schemasRepository->add($type);

        if (!is_null($schema)) {
            $content = (new Content())
                ->addMediaType(\Ouzo\Http\MediaType::APPLICATION_JSON, (new MediaType())
                    ->setSchema($schema)
                );
        }

        return $content;
    }

    public function extracted(ReflectionParameter $reflectionParameter): Content
    {
        $type = TypeUtils::getForParameter($reflectionParameter);
        $schema = SchemaUtils::create($type);

        $this->schemasRepository->add($type);

        return (new Content())
            ->addMediaType(\Ouzo\Http\MediaType::APPLICATION_JSON, (new MediaType())
                ->setSchema($schema)
            );
    }
}
