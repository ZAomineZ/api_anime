<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    /**
     * @var OpenApiFactoryInterface
     */
    private OpenApiFactoryInterface $decorated;

    /**
     * OpenApiFactory constructor.
     * @param OpenApiFactoryInterface $decorated
     */
    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param array $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $paths = $openApi->getPaths()->getPaths();

        $filteredPaths = new Model\Paths();
        /** @var Model\PathItem $pathItem */
        foreach ($paths as $path => $pathItem) {
//            if ($path === '/api/characters/{slug}') {
//                $pathItem->withParameters([
//                    'name' => 'slug',
//                    'in' => 'path',
//                    'description' => 'Trouver votre personnage avec le slug de ce dernier.',
//                    'type' => 'string',
//                    'required' => true,
//                    'example' => 'luffy'
//                ]);
//                dd($pathItem);
//            }
            $filteredPaths->addPath($path, $pathItem);
        }

        return $openApi->withPaths($filteredPaths);
    }
}
