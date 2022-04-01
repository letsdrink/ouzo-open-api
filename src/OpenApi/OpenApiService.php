<?php

namespace Ouzo\OpenApi;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\OpenApi\Customizer\OpenApiCustomizersRepository;
use Ouzo\OpenApi\Model\OpenApi;
use Ouzo\OpenApi\Model\OpenApiVersion;
use Ouzo\OpenApi\Service\ComponentsService;
use Ouzo\OpenApi\Service\PathsService;

class OpenApiService
{
    #[Inject]
    public function __construct(
        private PathsService $pathsService,
        private ComponentsService $componentsService,
        private OpenApiCustomizersRepository $openApiCustomizersRepository
    )
    {
    }

    public function create(): OpenApi
    {
        $openApi = (new OpenApi())
            ->setOpenapi(OpenApiVersion::V_3_0_1);

        $paths = $this->pathsService->create();
        $components = $this->componentsService->create();

        $openApi
            ->setPaths($paths)
            ->setComponents($components);

        $openApiCustomizers = $this->openApiCustomizersRepository->all();
        foreach ($openApiCustomizers as $openApiCustomizer) {
            $openApiCustomizer->customize($openApi);
        }

        return $openApi;
    }
}
