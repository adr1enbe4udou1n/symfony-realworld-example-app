<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleDeleteAction extends AbstractController
{
    public function __invoke(Article $data, EntityManagerInterface $em, TokenStorageInterface $token)
    {
        /** @var User */
        $user = $token->getToken()->getUser();

        if ($data->author->id !== $user->id) {
            return new JsonResponse('You cannot delete this article', 400);
        }

        $em->remove($data);
        $em->flush();
    }
}
