<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\Model\Server;
use ApiPlatform\Core\OpenApi\OpenApi;

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
            $path = str_replace('/api', '', $path);

            if ('/users/{id}' === $path) {
                continue;
            }

            if ('/users' === $path) {
                $pathItem = $pathItem->withPost($this->setRequestBodyDoc($pathItem->getPost(), 'Details of the new user to register'));
            }

            if ('/users/login' === $path) {
                $pathItem = $pathItem->withPost($this->setRequestBodyDoc($pathItem->getPost(), 'Credentials to use.'));
            }

            if ('/user' === $path) {
                $pathItem = $pathItem
                    ->withGet($this->removeResourceIdentifier(
                        $this->cleanupSuccessResponses($pathItem->getGet()))->withSecurity([['apiKey' => []]])
                    )
                    ->withPut(
                        $this->removeResourceIdentifier(
                            $this
                                ->setRequestBodyDoc($pathItem->getPut(), 'User details to update. At least one field is required.')
                                ->withSecurity([['apiKey' => []]])
                        )
                    );
            }

            if ('/profiles/celeb_{username}' === $path) {
                $pathItem = $pathItem->withGet(
                    $this->addUsernameParameter($pathItem->getGet()->withTags(['Profile']), 'Username of the profile to get')
                );
            }

            if ('/profiles/celeb_{username}/follow' === $path) {
                $pathItem = $pathItem->withPost(
                    $this->addUsernameParameter($pathItem->getPost()->withTags(['Profile']), 'Username of the profile you want to follow')->withSecurity([['apiKey' => []]])
                );
                $pathItem = $pathItem->withDelete(
                    $this->addUsernameParameter($pathItem->getDelete()->withTags(['Profile']), 'Username of the profile you want to unfollow')->withSecurity([['apiKey' => []]])
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

    private function removeResourceIdentifier(Operation $operation): Operation
    {
        $parameters = [];

        /** @var Parameter $parameter */
        foreach ($operation->getParameters() as $parameter) {
            if ('Resource identifier' === $parameter->getDescription()) {
                continue;
            }
            $parameters[] = $parameter;
        }

        return $operation->withParameters($parameters);
    }

    private function addUsernameParameter(Operation $operation, string $description): Operation
    {
        return $this->removeResourceIdentifier($operation->withParameters([
            new Parameter('username', 'path', $description),
        ]));
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
