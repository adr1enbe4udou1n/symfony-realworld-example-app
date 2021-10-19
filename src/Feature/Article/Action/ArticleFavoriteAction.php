<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Entity\User;
use App\Feature\Article\Response\SingleArticleResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleFavoriteAction extends AbstractController
{
    public function __invoke(Article $data, EntityManagerInterface $em, TokenStorageInterface $token)
    {
        /** @var User */
        $user = $token->getToken()->getUser();

        $user->favorite($data);
        $em->flush();

        return new SingleArticleResponse($data);
    }
}
