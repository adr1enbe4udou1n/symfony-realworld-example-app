<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Feature\Article\DTO\ArticleDTO;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleUpdateAction extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
    ) {
    }

    public function __invoke(Article $article)
    {
        $response = new SingleArticleResponse();
        $response->article = new ArticleDTO();

        return $response;
    }
}
