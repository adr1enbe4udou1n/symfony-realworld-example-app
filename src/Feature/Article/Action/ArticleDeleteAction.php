<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleDeleteAction extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
    ) {
    }

    public function __invoke(Article $article)
    {
    }
}
