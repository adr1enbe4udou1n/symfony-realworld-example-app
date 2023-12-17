<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\Comment\CommentCreateController;
use App\Controller\Comment\CommentDeleteController;
use App\Controller\Comment\CommentListController;
use App\Dto\Comment\MultipleCommentsResponse;
use App\Dto\Comment\NewCommentRequest;
use App\Dto\Comment\SingleCommentResponse;

#[Get(
    name: 'GetArticleComments',
    uriTemplate: '/articles/{slug}/comments',
    controller: CommentListController::class,
    output: MultipleCommentsResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get comments for an article.',
        description: 'Get the comments for an article. Auth is optional',
        tags: ['Comments'],
        security: [],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to get comments for'
            ),
        ],
    )
)]
#[Post(
    name: 'CreateArticleComment',
    uriTemplate: '/articles/{slug}/comments',
    controller: CommentCreateController::class,
    security: 'is_granted("ROLE_USER")',
    input: NewCommentRequest::class,
    output: SingleCommentResponse::class,
    openapi: new Operation(
        summary: 'Create a comment for an article.',
        description: 'Create a comment for an article. Auth is required',
        tags: ['Comments'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to create a comment for'
            ),
        ],
    )
)]
#[Delete(
    name: 'DeleteArticleComment',
    uriTemplate: '/articles/{slug}/comments/{commentId}',
    controller: CommentDeleteController::class,
    security: 'is_granted("ROLE_USER")',
    output: false,
    read: false,
    openapi: new Operation(
        summary: 'Delete a comment for an article.',
        description: 'Delete a comment for an article. Auth is required',
        tags: ['Comments'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to delete a comment for'
            ),
            new Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Id of the comment that you want to delete'
            ),
        ],
    )
)]
class CommentResource
{
}
