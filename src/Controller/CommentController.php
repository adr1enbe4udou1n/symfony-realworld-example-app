<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Feature\Comment\DTO\NewCommentDTO;
use App\Feature\Comment\Response\MultipleCommentsResponse;
use App\Feature\Comment\Response\SingleCommentResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/articles/{slug}/comments')]
class CommentController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetArticleComments',
        summary: 'Get comments for an article.',
        description: 'Get the comments for an article. Auth is optional',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to get comments for',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: MultipleCommentsResponse::class)
                )
            ),
        ]
    )]
    public function list(Article $article): Response
    {
        return $this->json([]);
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        operationId: 'CreateArticleComment',
        summary: 'Create a comment for an article.',
        description: 'Create a comment for an article. Auth is required',
        tags: ['Comments'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to create a comment for',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: NewCommentDTO::class)
            )
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleCommentResponse::class)
                )
            ),
        ]
    )]
    public function create(Article $article, NewCommentDTO $data): Response
    {
        return $this->json([]);
    }

    #[Route('/{commentId}', methods: ['DELETE'])]
    #[Entity('comment', options: ['commentId' => 'id'])]
    #[OA\Delete(
        operationId: 'DeleteArticleComment',
        summary: 'Delete a comment for an article.',
        description: 'Delete a comment for an article. Auth is required',
        tags: ['Comments'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to delete a comment for',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'commentId',
                in: 'path',
                description: 'ID of the comment you want to delete',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleCommentResponse::class)
                )
            ),
        ]
    )]
    public function delete(Article $article, Comment $commentId): Response
    {
        return $this->json([]);
    }
}
