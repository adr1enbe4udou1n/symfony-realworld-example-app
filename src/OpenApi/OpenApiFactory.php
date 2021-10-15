<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\Model\Server;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\String\UnicodeString;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $paths = $openApi->getPaths()->getPaths();

        $filteredPaths = new Model\Paths();

        /** @var PathItem $pathItem */
        foreach ($paths as $path => $pathItem) {
            $path = (new UnicodeString($path))->trimStart('/api')->prepend('/');

            if ($path->equalsTo('/users/{id}')) {
                continue;
            }

            if ($path->equalsTo('/users')) {
                $pathItem = $pathItem->withPost($this->setRequestBodyDoc($pathItem->getPost(), 'Details of the new user to register'));
            }

            if ($path->equalsTo('/users/login')) {
                $pathItem = $pathItem->withPost($this->setRequestBodyDoc($pathItem->getPost(), 'Credentials to use.'));
            }

            if ($path->equalsTo('/user')) {
                $pathItem = $pathItem
                    ->withGet($this->cleanupSuccessResponses($pathItem->getGet())->withSecurity([['apiKey' => []]]))
                    ->withPut(
                        $this
                            ->setRequestBodyDoc($pathItem->getPut(), 'User details to update. At least one field is required.')
                            ->withSecurity([['apiKey' => []]])
                    );
            }

            $filteredPaths->addPath($path, $pathItem);
        }

        return $openApi
            ->withPaths($filteredPaths)
            ->withServers([
                new Server('/api'),
            ])
            ->withSecurity([]);
    }

    private function cleanupSuccessResponses(Operation $operation): Operation
    {
        $responses = [];

        /** @var Response $response */
        foreach ($operation->getResponses() as $code => $response) {
            $responses[$code] = in_array($code, [200, 201], true) ? new Response('Success', $response->getContent()) : $response;
        }

        return $operation->withResponses($responses);
    }

    private function setRequestBodyDoc(Operation $operation, string $comment): Operation
    {
        return $this->cleanupSuccessResponses($operation->withRequestBody(
            $operation->getRequestBody()->withDescription($comment)
        ));
    }
}
