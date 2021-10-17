<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Feature\Article\DTO\ArticleDTO;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleCreateAction extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
        private TagRepository $tags,
    ) {
    }

    public function __invoke(Article $article)
    {
        $response = new SingleArticleResponse();
        $response->article = new ArticleDTO();

        return $response;
    }
}
