<?php

namespace App\Controller\Comment;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentDeleteController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
        private CommentRepository $comments,
    ) {
    }

    public function __invoke($slug, $id)
    {
        $article = $this->articles->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException();
        }

        $comment = $this->comments->findOneBy(['id' => $id]);

        if (!$comment) {
            throw $this->createNotFoundException();
        }

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
