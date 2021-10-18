<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use App\Feature\Article\Response\SingleArticleResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleGetAction extends AbstractController
{
    public function __invoke(Article $data)
    {
        return new SingleArticleResponse($data);
    }
}
