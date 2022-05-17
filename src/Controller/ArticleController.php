<?php

namespace App\Controller;

use App\Entity\Article;
use App\Feature\Article\DTO\NewArticleDTO;
use App\Feature\Article\DTO\UpdateArticleDTO;
use App\Feature\Article\Response\MultipleArticlesResponse;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(private ArticleRepository $articles, private TokenStorageInterface $token)
    {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetArticles',
        summary: 'Get recent articles globally.',
        description: 'Get most recent articles globally. Use query parameters to filter results. Auth is optional',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Limit number of articles returned (default is 20)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset/skip number of articles (default is 0)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'author',
                in: 'query',
                description: 'Filter by author (username)',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'favorited',
                in: 'query',
                description: 'Filter by favorites of a user (username)',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'tag',
                in: 'query',
                description: 'Filter by tag',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: MultipleArticlesResponse::class)
                )
            ),
        ]
    )]
    public function list(): Response
    {
        $this->articles->findBy([], ['createdAt' => 'DESC'], 20, 0);

        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }

    #[Route('/feed', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetArticlesFeed',
        summary: 'Get recent articles from users you follow.',
        description: 'Get most recent articles from users you follow. Use query parameters to limit. Auth is required',
        tags: ['Articles'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Limit number of articles returned (default is 20)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset/skip number of articles (default is 0)',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: MultipleArticlesResponse::class)
                )
            ),
        ]
    )]
    public function feed(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }

    #[Route('/{slug}', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetArticle',
        summary: 'Get an article',
        description: 'Get an article. Auth not required',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article to get',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function get(Article $article): Response
    {
        return $this->json(SingleArticleResponse::make($article, $this->token));
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        operationId: 'CreateArticle',
        summary: 'Create an article.',
        description: 'Create an article. Auth is required',
        tags: ['Articles'],
        requestBody: new OA\RequestBody(
            ref: new OA\Schema(type: 'string', ref: new Model(type: NewArticleDTO::class))
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function create(NewArticleDTO $data): Response
    {
        return $this->json([]);
    }

    #[Route('/{slug}', methods: ['PUT'])]
    #[OA\Put(
        operationId: 'UpdateArticle',
        summary: 'Update an article.',
        description: 'Update an article. Auth is required',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article to update',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            ref: new OA\Schema(type: 'string', ref: new Model(type: UpdateArticleDTO::class))
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function update(Article $article, UpdateArticleDTO $data): Response
    {
        return $this->json([]);
    }

    #[Route('/{slug}', methods: ['DELETE'])]
    #[OA\Delete(
        operationId: 'DeleteArticle',
        summary: 'Delete an article.',
        description: 'Delete an article. Auth is required',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article to delete',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function delete(Article $article): Response
    {
        return $this->json([]);
    }

    #[Route('/{slug}/favorite', methods: ['POST'])]
    #[OA\Post(
        operationId: 'CreateArticleFavorite',
        summary: 'Favorite an article.',
        description: 'Favorite an article. Auth is required',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to favorite',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function favorite(Article $article): Response
    {
        return $this->json([]);
    }

    #[Route('/{slug}/favorite', methods: ['DELETE'])]
    #[OA\Delete(
        operationId: 'DeleteArticleFavorite',
        summary: 'Unfavorite an article.',
        description: 'Unfavorite an article. Auth is required',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to unfavorite',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function unfavorite(Article $article): Response
    {
        return $this->json([]);
    }
}
