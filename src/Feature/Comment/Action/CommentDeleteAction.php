<?php

namespace App\Feature\Comment\Action;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentDeleteAction extends AbstractController
{
    public function __construct(
        private TokenStorageInterface $token,
        private EntityManagerInterface $em,
        private CommentRepository $comments,
    ) {
    }

    public function __invoke(Article $article, Request $request)
    {
        $comment = $this->comments->find($request->attributes->get('id'));

        if (!$comment) {
            return new JsonResponse('No comment of this id found', 404);
        }

        if ($comment->article->id !== $article->id) {
            return new JsonResponse('This comment is not associate with requested article', 400);
        }

        /** @var User */
        $user = $this->token->getToken()->getUser();

        if ($comment->author->id !== $user->id && $article->author->id !== $user->id) {
            return new JsonResponse('You cannot delete this comment', 400);
        }

        $this->em->remove($comment);
        $this->em->flush();
    }
}
