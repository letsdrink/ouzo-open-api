<?php

namespace Ouzo\OpenApi;

use Ouzo\OpenApi\Appender\ComponentsAppender;
use Ouzo\OpenApi\Appender\PathsAppender;
use Ouzo\OpenApi\Model\Info;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\OpenApiVersion;
use Ouzo\OpenApi\Model\Server;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Utilities\Chain\ChainExecutor;
use Ouzo\Utilities\Functions;

class OpenApiFactory
{
    #[Inject]
    public function __construct(
        private PathsAppender $pathsAppender,
        private ComponentsAppender $componentsAppender
    )
    {
    }

    public function create(string $title, string $description, string $systemVersion, string $url): OpenApi
    {
        $openApi = (new OpenApi())
            ->setOpenapi(OpenApiVersion::V_3_0_1)
            ->setInfo((new Info())
                ->setTitle($title)
                ->setDescription($description)
                ->setVersion($systemVersion)
            )
            ->setServers([
                (new Server())
                    ->setUrl($url),
            ]);

        $chainExecutor = $this->getChainExecutor();
        return $chainExecutor->execute($openApi, Functions::identity());
    }

    private function getChainExecutor(): ChainExecutor
    {
        return (new ChainExecutor())
            ->add($this->pathsAppender)
            ->add($this->componentsAppender);
    }
}
