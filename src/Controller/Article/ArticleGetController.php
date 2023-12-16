<?php

namespace App\Controller\Article;

use App\Dto\Article\SingleArticleResponse;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleGetController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles
    ) {
    }

    public function __invoke($slug)
    {
        $article = $this->articles->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException();
        }

        /** @var User */
        $user = $this->getUser();

        return $this->json(new SingleArticleResponse($article, $user));
    }
}
