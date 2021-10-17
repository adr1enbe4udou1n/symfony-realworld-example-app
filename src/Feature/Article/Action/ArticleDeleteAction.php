<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleDeleteAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
        private TokenStorageInterface $token,
    ) {
    }

    public function __invoke(Article $article)
    {
        /** @var User */
        $user = $this->token->getToken()->getUser();

        if ($article->author->id !== $user->id) {
            return new JsonResponse('You cannot delete this article', 400);
        }

        $this->em->remove($article);
        $this->em->flush();
    }
}
