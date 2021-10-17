<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
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

        $paths = new Paths();

        $paths->addPath('/users', (new PathItem())
            ->withPost(
                (new Operation('login', ['User and Authentication']))
                    ->withSummary('Register a new user')
                    ->withDescription('Register a new user')
                    ->withRequestBody(
                        new RequestBody('Details of the new user to register', $openApi->getPaths()
                            ->getPath('/api/users')
                            ->getPost()
                            ->getRequestBody()
                            ->getContent()
                        )
                    )
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/users')->getPost())
                    )
            )
        );
        $paths->addPath('/users/login', (new PathItem())
            ->withPost(
                (new Operation('register', ['User and Authentication']))
                    ->withSummary('Existing user login')
                    ->withDescription('Login for existing user')
                    ->withRequestBody(
                        new RequestBody('Credentials to use.', $openApi->getPaths()
                            ->getPath('/api/users/login')
                            ->getPost()
                            ->getRequestBody()
                            ->getContent()
                        )
                    )
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/users/login')->getPost())
                    )
            )
        );
        $paths->addPath('/user', (new PathItem())
            ->withGet(
                (new Operation('currentUser', ['User and Authentication']))
                    ->withSummary('Get current user')
                    ->withDescription('Gets the currently logged-in user')
                    ->withSecurity([['apiKey' => []]])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/user')->getGet())
                    )
            )
            ->withPut(
                (new Operation('updateUser', ['User and Authentication']))
                    ->withSummary('Update current user')
                    ->withDescription('Updated user information for current user')
                    ->withSecurity([['apiKey' => []]])
                    ->withRequestBody(
                        new RequestBody('User details to update. At least one field is required.', $openApi->getPaths()
                            ->getPath('/api/user')
                            ->getPut()
                            ->getRequestBody()
                            ->getContent()
                        )
                    )
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/user')->getPut())
                    )
            )
        );
        $paths->addPath('/profiles/celeb_{username}', (new PathItem())
            ->withGet(
                (new Operation('profile', ['Profile']))
                    ->withSummary('Get a profile')
                    ->withDescription('Get a profile of a user of the system. Auth is optional')
                    ->withParameters([
                        new Parameter('username', 'path', 'Username of the profile to get'),
                    ])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/profiles/celeb_{username}')->getGet())
                    )
            )
        );
        $paths->addPath('/profiles/celeb_{username}/follow', (new PathItem())
            ->withPost(
                (new Operation('follow', ['Profile']))
                    ->withSummary('Follow a user')
                    ->withDescription('Follow a user by username')
                    ->withSecurity([['apiKey' => []]])
                    ->withParameters([
                        new Parameter('username', 'path', 'Username of the profile you want to follow'),
                    ])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/profiles/celeb_{username}/follow')->getPost())
                    )
            )
            ->withDelete(
                (new Operation('unfollow', ['Profile']))
                    ->withSummary('Unfollow a user')
                    ->withDescription('Unfollow a user by username')
                    ->withSecurity([['apiKey' => []]])
                    ->withParameters([
                        new Parameter('username', 'path', 'Username of the profile you want to unfollow'),
                    ])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/profiles/celeb_{username}/follow')->getPost())
                    )
            )
        );
        $paths->addPath('/tags', (new PathItem())
            ->withGet(
                (new Operation('tags', ['Tags']))
                    ->withSummary('Get tags')
                    ->withDescription('Get tags. Auth not required')
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/tags')->getGet())
                    )
            )
        );

        return $openApi
            ->withPaths($paths)
            ->withServers([
                new Server('/api'),
            ])
            ->withSecurity([]);
    }

    private function getResponses(Operation $operation): array
    {
        $responses = [];

        /** @var Response $response */
        foreach ($operation->getResponses() as $code => $response) {
            if (200 === $code) {
                $responses[$code] = new Response('Success', $response->getContent());
            }
        }

        return $responses;
    }
}
