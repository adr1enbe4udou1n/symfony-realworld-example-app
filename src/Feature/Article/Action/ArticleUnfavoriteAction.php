<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Entity\User;
use App\Feature\Article\Response\SingleArticleResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleUnfavoriteAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface $token,
    ) {
    }

    public function __invoke(Article $data)
    {
        /** @var User */
        $user = $this->token->getToken()->getUser();

        $user->unfavorite($data);
        $this->em->flush();

        return new SingleArticleResponse($data);
    }
}
