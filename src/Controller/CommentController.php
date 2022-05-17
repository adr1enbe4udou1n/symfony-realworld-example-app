<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Feature\Comment\Request\NewCommentRequest;
use App\Feature\Comment\Response\MultipleCommentsResponse;
use App\Feature\Comment\Response\SingleCommentResponse;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route('/articles/{slug}/comments')]
class CommentController extends AbstractController
{
    public function __construct(
        private CommentRepository $comments,
        private TokenStorageInterface $token,
        private EntityManagerInterface $em,
    ) {
    }

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
        return $this->json(
            MultipleCommentsResponse::make($this->comments->findByArticle($article), $this->token)
        );
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
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
                ref: new Model(type: NewCommentRequest::class)
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
    public function create(Article $article, NewCommentRequest $data, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, 422);
        }

        /** @var User */
        $user = $this->getUser();

        $comment = new Comment();
        $comment->body = $data->comment->body;
        $comment->author = $user;
        $comment->article = $article;

        $this->em->persist($comment);
        $this->em->flush();

        return $this->json(SingleCommentResponse::make($comment, $this->token));
    }

    #[Route('/{commentId}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
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
    public function delete(Article $article, Comment $comment): Response
    {
        if ($comment->article->id !== $article->id) {
            return $this->json([
                'message' => 'This comment is not associate with requested article',
            ], 400);
        }

        /** @var User */
        $user = $this->getUser();

        if ($comment->author->id !== $user->id && $article->author->id !== $user->id) {
            return $this->json([
                'message' => 'You cannot delete this comment',
            ], 400);
        }

        $this->em->remove($comment);
        $this->em->flush();

        return $this->json([])->setStatusCode(204);
    }
}
