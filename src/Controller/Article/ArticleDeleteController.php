<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleDeleteController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
    ) {
    }

    public function __invoke(Article $article)
    {
        /** @var User */
        $user = $this->getUser();

        if ($article->author->id !== $user->id) {
            return $this->json(['message' => 'You cannot delete this article'], 400);
        }

        $this->em->remove($article);
        $this->em->flush();

        return $this->json([])->setStatusCode(204);
    }
}
