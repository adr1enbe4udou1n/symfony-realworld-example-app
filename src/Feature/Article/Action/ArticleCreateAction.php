<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleCreateAction extends AbstractController
{
    public function __invoke(Article $data, EntityManagerInterface $em, ArticleRepository $articles)
    {
        if ($articles->findOneBy(['title' => $data->title])) {
            return new JsonResponse('Article with this title already exist', 400);
        }

        $em->persist($data);
        $em->flush();

        return new SingleArticleResponse($data);
    }
}
