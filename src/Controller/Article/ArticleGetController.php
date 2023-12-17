<?php

namespace App\Controller\Article;

use App\Dto\Article\SingleArticleResponse;
use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleGetController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles
    ) {
    }

    public function __invoke(Article $article)
    {
        /** @var User */
        $user = $this->getUser();

        return $this->json(new SingleArticleResponse($article, $user));
    }
}
