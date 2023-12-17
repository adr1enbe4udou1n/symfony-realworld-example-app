<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\Article\ArticleCreateController;
use App\Controller\Article\ArticleDeleteController;
use App\Controller\Article\ArticleFavoriteController;
use App\Controller\Article\ArticleFeedController;
use App\Controller\Article\ArticleGetController;
use App\Controller\Article\ArticleListController;
use App\Controller\Article\ArticleUnfavoriteController;
use App\Controller\Article\ArticleUpdateController;
use App\Dto\Article\MultipleArticlesResponse;
use App\Dto\Article\NewArticleRequest;
use App\Dto\Article\SingleArticleResponse;
use App\Dto\Article\UpdateArticleRequest;

#[Get(
    name: 'GetArticles',
    uriTemplate: '/articles',
    controller: ArticleListController::class,
    output: MultipleArticlesResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get recent articles globally.',
        description: 'Get most recent articles globally. Use query parameters to filter results. Auth is optional',
        tags: ['Articles'],
        security: [],
        parameters: [
            new Parameter(
                name: 'limit',
                in: 'query',
                description: 'Limit number of articles returned (default is 20)',
            ),
            new Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset/skip number of articles (default is 0)',
            ),
            new Parameter(
                name: 'tag',
                in: 'query',
                description: 'Filter by tag',
            ),
            new Parameter(
                name: 'author',
                in: 'query',
                description: 'Filter by author (username)',
            ),
            new Parameter(
                name: 'favorited',
                in: 'query',
                description: 'Filter by favorited (username)',
            ),
        ],
    )
)]
#[Get(
    name: 'GetArticlesFeed',
    uriTemplate: '/articles/feed',
    controller: ArticleFeedController::class,
    output: MultipleArticlesResponse::class,
    security: 'is_granted("ROLE_USER")',
    read: false,
    openapi: new Operation(
        summary: 'Get recent articles from users you follow.',
        description: 'Get most recent articles from users you follow. Use query parameters to limit. Auth is required',
        tags: ['Articles'],
        parameters: [
            new Parameter(
                name: 'limit',
                in: 'query',
                description: 'Limit number of articles returned (default is 20)',
            ),
            new Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset/skip number of articles (default is 0)',
            ),
        ],
    )
)]
#[Get(
    name: 'GetArticle',
    uriTemplate: '/articles/{slug}',
    controller: ArticleGetController::class,
    output: SingleArticleResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get an article',
        description: 'Get an article. Auth not required',
        tags: ['Articles'],
        security: [],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article to get',
            ),
        ],
    )
)]
#[Post(
    name: 'CreateArticle',
    uriTemplate: '/articles',
    controller: ArticleCreateController::class,
    security: 'is_granted("ROLE_USER")',
    input: NewArticleRequest::class,
    output: SingleArticleResponse::class,
    openapi: new Operation(
        summary: 'Create an article',
        description: 'Create an article. Auth is required',
        tags: ['Articles'],
    )
)]
#[Put(
    name: 'UpdateArticle',
    uriTemplate: '/articles/{slug}',
    controller: ArticleUpdateController::class,
    security: 'is_granted("ROLE_USER")',
    input: UpdateArticleRequest::class,
    output: SingleArticleResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Update an article.',
        description: 'Update an article. Auth is required',
        tags: ['Articles'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article to update',
            ),
        ],
    )
)]
#[Delete(
    name: 'DeleteArticle',
    uriTemplate: '/articles/{slug}',
    controller: ArticleDeleteController::class,
    security: 'is_granted("ROLE_USER")',
    output: false,
    read: false,
    openapi: new Operation(
        summary: 'Delete an article.',
        description: 'Delete an article. Auth is required',
        tags: ['Articles'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article to delete',
            ),
        ],
    )
)]
#[Post(
    name: 'CreateArticleFavorite',
    uriTemplate: '/articles/{slug}/favorite',
    controller: ArticleFavoriteController::class,
    security: 'is_granted("ROLE_USER")',
    input: false,
    output: SingleArticleResponse::class,
    openapi: new Operation(
        summary: 'Favorite an article.',
        description: 'Favorite an article. Auth is required',
        tags: ['Favorites'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to favorite',
            ),
        ],
    )
)]
#[Delete(
    name: 'DeleteArticleFavorite',
    uriTemplate: '/articles/{slug}/favorite',
    controller: ArticleUnfavoriteController::class,
    security: 'is_granted("ROLE_USER")',
    output: SingleArticleResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Unfavorite an article.',
        description: 'Unfavorite an article. Auth is required',
        tags: ['Favorites'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to unfavorite',
            ),
        ],
    )
)]
class ArticleResource
{
}
