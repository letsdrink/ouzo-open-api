<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Extractor\DiscriminatorExtractor;
use Ouzo\OpenApi\Model\RefSchema;
use Ouzo\OpenApi\Model\Response;
use Ouzo\OpenApi\Util\ComponentPathHelper;
use Ouzo\OpenApi\Util\TypeConverter;
use Ouzo\Utilities\Chain\Chain;

class ResponsesAppender implements PathAppender
{
    #[Inject]
    public function __construct(private DiscriminatorExtractor $discriminatorExtractor)
    {
    }

    /** @param PathContext $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $internalResponse = $param->getInternalPath()->getInternalResponse();

        $typeWrapper = $internalResponse->getTypeWrapper();

        $internalDiscriminators = null;
        if (!$typeWrapper?->isPrimitive()) {
            $internalDiscriminators = $this->discriminatorExtractor->extract($typeWrapper?->get());
        }

        $response = (new Response())
            ->setDescription('success');

        if (!is_null($internalDiscriminators)) {
            $oneOf = null;
            foreach ($internalDiscriminators as $internalDiscriminator) {
                $oneOf[] = (new RefSchema())
                    ->setRef(ComponentPathHelper::getPathForReflectionClass($internalDiscriminator->getReflectionClass()));
            }
            $response->setContent([
                'application/json' => [
                    'schema' => [
                        'oneOf' => $oneOf,
                    ],
                ],
            ]);
        } else {
            $schema = TypeConverter::convertTypeWrapperToSchema($typeWrapper);
            if (!is_null($schema)) {
                $response->setContent([
                    'application/json' => [
                        'schema' => $schema,
                    ],
                ]);
            }
        }

        $param->getPath()->setResponses([$internalResponse->getResponseCode() => $response]);

        return $next->proceed($param);
    }
}
