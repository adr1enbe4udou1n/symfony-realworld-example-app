<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleFavoriteAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface $token,
    ) {
    }

    public function __invoke(Article $article)
    {
        /** @var User */
        $user = $this->token->getToken()->getUser();

        $user->favorite($article);
        $this->em->flush();

        return $article;
    }
}
