<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private const BASE_PATH = '/api';

    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $customPaths = new Paths();

        $paths = $openApi->getPaths()->getPaths();

        foreach ($paths as $path => $pathItem) {
            $customPaths->addPath(\str_replace(self::BASE_PATH, '', $path), $pathItem);
        }

        $openApi = $openApi->withPaths($customPaths);

        return $openApi->withServers([
            new Server(url: self::BASE_PATH),
        ]);
    }
}
