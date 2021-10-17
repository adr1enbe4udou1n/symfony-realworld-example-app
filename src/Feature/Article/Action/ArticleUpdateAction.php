<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleUpdateAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
        private TokenStorageInterface $token,
    ) {
    }

    public function __invoke(Article $data)
    {
        if ($this->articles->findOneBy(['title' => $data->title])) {
            return new JsonResponse('Article with this title already exist', 400);
        }

        if ($this->token->getToken()->getUser()->getUserIdentifier() !== $data->author->getUserIdentifier()) {
            return new JsonResponse('You cannot not edit article of other authors', 400);
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
