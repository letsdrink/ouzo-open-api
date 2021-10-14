<?php

namespace Ouzo\OpenApi\Appender;

use Ouzo\OpenApi\CachedInternalPathProvider;
use Ouzo\OpenApi\InternalPath;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\Path;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\ChainExecutor;
use Ouzo\Utilities\Chain\Interceptor;

class PathsAppender implements Interceptor
{
    #[Inject]
    public function __construct(
        private CachedInternalPathProvider $cachedInternalPathProvider,
        private ParametersAppender $parametersAppender,
        private RequestBodyAppender $requestBodyAppender,
        private ResponsesAppender $responsesAppender
    )
    {
    }

    /** @param OpenApi $param */
    public function handle(mixed $param, Chain $next): mixed
    {
        $chainExecutor = $this->getChainExecutor();
        $internalPaths = $this->cachedInternalPathProvider->get();

        $internalPathsGroupedByUri = Arrays::groupBy($internalPaths, fn(InternalPath $path) => $path->getInternalPathDetails()->getUri());

        $paths = [];
        foreach ($internalPathsGroupedByUri as $uri => $internalPaths) {
            $tmpPaths = [];
            /** @var InternalPath $internalPath */
            foreach ($internalPaths as $internalPath) {
                $internalPathInfo = $internalPath->getInternalPathDetails();

                $path = (new Path())
                    ->setTags([$internalPathInfo->getTag()])
                    ->setSummary($internalPathInfo->getSummary())
                    ->setOperationId($internalPathInfo->getOperationId());

                $pathContext = new PathContext($path, $internalPath);
                $path = $chainExecutor->execute($pathContext, fn(PathContext $context) => $context->getPath());

                $tmpPaths[$internalPathInfo->getHttpMethod()] = $path;
            }
            $paths[$uri] = $tmpPaths;
        }

        $param->setPaths($paths);

        return $next->proceed($param);
    }

    private function getChainExecutor(): ChainExecutor
    {
        return (new ChainExecutor())
            ->add($this->parametersAppender)
            ->add($this->requestBodyAppender)
            ->add($this->responsesAppender);
    }
}
