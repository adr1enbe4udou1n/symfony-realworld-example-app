<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleCreateAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
        private TagRepository $tags,
    ) {
    }

    public function __invoke(Article $data)
    {
        if ($this->articles->findOneBy(['title' => $data->title])) {
            return new JsonResponse('Article with this title already exist', 400);
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
