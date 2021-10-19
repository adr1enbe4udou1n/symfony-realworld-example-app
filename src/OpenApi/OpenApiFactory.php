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
                        $this->getResponses($openApi->getPaths()->getPath('/api/profiles/celeb_{name}')->getGet())
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
                        $this->getResponses($openApi->getPaths()->getPath('/api/profiles/celeb_{name}/follow')->getPost())
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
                        $this->getResponses($openApi->getPaths()->getPath('/api/profiles/celeb_{name}/follow')->getPost())
                    )
            )
        );
        $paths->addPath('/tags', (new PathItem())
            ->withGet(
                (new Operation('tagsList', ['Tags']))
                    ->withSummary('Get tags')
                    ->withDescription('Get tags. Auth not required')
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/tags')->getGet())
                    )
            )
        );
        $paths->addPath('/articles', (new PathItem())
            ->withGet(
                (new Operation('articlesList', ['Articles']))
                    ->withSummary('Get recent articles globally')
                    ->withDescription('Get most recent articles globally. Use query parameters to filter results. Auth is optional')
                    ->withParameters([
                        new Parameter('limit', 'query', 'Limit number of articles returned (default is 20)'),
                        new Parameter('offset', 'query', 'Offset/skip number of articles (default is 0)'),
                        new Parameter('author', 'query', 'Filter by author (username)'),
                        new Parameter('tag', 'query', 'Filter by tag'),
                        new Parameter('favorited', 'query', 'Filter by favorites of a user (username)'),
                    ])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles')->getGet())
                    )
            )
            ->withPost(
                (new Operation('articlesCreate', ['Articles']))
                    ->withSummary('Create an article')
                    ->withDescription('Create an article. Auth is required')
                    ->withSecurity([['apiKey' => []]])
                    ->withRequestBody(
                        new RequestBody('Article to create', $openApi->getPaths()
                            ->getPath('/api/articles')
                            ->getPost()
                            ->getRequestBody()
                            ->getContent()
                        )
                    )
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles')->getPost())
                    )
            )
        );
        $paths->addPath('/articles/feed', (new PathItem())
            ->withGet(
                (new Operation('articlesFeed', ['Articles']))
                    ->withSummary('Get recent articles from users you follow')
                    ->withDescription('Get most recent articles from users you follow. Use query parameters to limit. Auth is required')
                    ->withParameters([
                        new Parameter('limit', 'query', 'Limit number of articles returned (default is 20)'),
                        new Parameter('offset', 'query', 'Offset/skip number of articles (default is 0)'),
                    ])
                    ->withSecurity([['apiKey' => []]])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/feed')->getGet())
                    )
            )
        );
        $paths->addPath('/articles/{slug}', (new PathItem())
            ->withGet(
                (new Operation('articlesGet', ['Articles']))
                    ->withSummary('Get an article')
                    ->withDescription('Get an article. Auth not required')
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article to get'),
                    ])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/{slug}')->getGet())
                    )
            )
            ->withPut(
                (new Operation('articlesUpdate', ['Articles']))
                    ->withSummary('Update an article')
                    ->withDescription('Update an article. Auth is required')
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article to update'),
                    ])
                    ->withSecurity([['apiKey' => []]])
                    ->withRequestBody(
                        new RequestBody('Article to update', $openApi->getPaths()
                            ->getPath('/api/articles/{slug}')
                            ->getPut()
                            ->getRequestBody()
                            ->getContent()
                        )
                    )
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/{slug}')->getPut())
                    )
            )
            ->withDelete(
                (new Operation('articlesDelete', ['Articles']))
                    ->withSummary('Delete an article')
                    ->withDescription('Delete an article. Auth is required')
                    ->withSecurity([['apiKey' => []]])
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article to delete'),
                    ])
                    ->withResponses([
                        200 => new Response('Success'),
                    ])
            )
        );
        $paths->addPath('/articles/{slug}/comments', (new PathItem())
            ->withGet(
                (new Operation('commentsGet', ['Comments']))
                    ->withSummary('Get comments for an article')
                    ->withDescription('Get the comments for an article. Auth is optional')
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article that you want to get comments for'),
                    ])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/{slug}/comments')->getGet())
                    )
            )
            ->withPost(
                (new Operation('commentsCreate', ['Comments']))
                    ->withSummary('Create a comment for an article')
                    ->withDescription('Create a comment for an article. Auth is required')
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article that you want to create a comment for'),
                    ])
                    ->withSecurity([['apiKey' => []]])
                    ->withRequestBody(
                        new RequestBody('Comment you want to create', $openApi->getPaths()
                            ->getPath('/api/articles/{slug}/comments')
                            ->getPost()
                            ->getRequestBody()
                            ->getContent()
                        )
                    )
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/{slug}/comments')->getPost())
                    )
            )
        );
        $paths->addPath('/articles/{slug}/comments/{commentId}', (new PathItem())
            ->withDelete(
                (new Operation('commentsDelete', ['Comments']))
                    ->withSummary('Delete a comment for an article')
                    ->withDescription('Delete a comment for an article. Auth is required')
                    ->withSecurity([['apiKey' => []]])
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article that you want to delete a comment for'),
                        new Parameter('commentId', 'path', 'ID of the comment you want to delete'),
                    ])
                    ->withResponses([
                        200 => new Response('Success'),
                    ])
            )
        );

        $paths->addPath('/articles/{slug}/favorite', (new PathItem())
            ->withPost(
                (new Operation('articlesFavorite', ['Favorites']))
                    ->withSummary('Favorite an article')
                    ->withDescription('Favorite an article. Auth is required')
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article that you want to favorite'),
                    ])
                    ->withSecurity([['apiKey' => []]])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/{slug}/favorite')->getPost())
                    )
            )
            ->withDelete(
                (new Operation('articlesUnfavorite', ['Favorites']))
                    ->withSummary('Unfavorite an article')
                    ->withDescription('Unfavorite an article. Auth is required')
                    ->withParameters([
                        new Parameter('slug', 'path', 'Slug of the article that you want to unfavorite'),
                    ])
                    ->withSecurity([['apiKey' => []]])
                    ->withResponses(
                        $this->getResponses($openApi->getPaths()->getPath('/api/articles/{slug}/favorite')->getPost())
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
            if (in_array($code, [200, 201])) {
                $responses[$code] = new Response('Success', $response->getContent());
            }
        }

        return $responses;
    }
}
