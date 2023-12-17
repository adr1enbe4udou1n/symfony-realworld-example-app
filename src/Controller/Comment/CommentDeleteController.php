<?php

namespace App\Controller\Comment;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentDeleteController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        Article $article,
        #[MapEntity(mapping: ['commentId' => 'id'])] Comment $comment
    ) {
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
