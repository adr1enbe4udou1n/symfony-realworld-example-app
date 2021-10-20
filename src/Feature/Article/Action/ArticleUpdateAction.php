<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleUpdateAction extends AbstractController
{
    public function __invoke(Article $data, EntityManagerInterface $em, ArticleRepository $articles, TokenStorageInterface $token)
    {
        if (($article = $articles->findOneBy(['title' => $data->title])) && $article->id !== $data->id) {
            return new JsonResponse('Article with this title already exist', 400);
        }

        if ($token->getToken()->getUser()->getUserIdentifier() !== $data->author->getUserIdentifier()) {
            return new JsonResponse('You cannot not edit article of other authors', 400);
        }

        $em->persist($data);
        $em->flush();

        return new SingleArticleResponse($data);
    }
}
