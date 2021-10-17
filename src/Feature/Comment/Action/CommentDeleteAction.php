<?php

namespace App\Feature\Comment\Action;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentDeleteAction extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
    ) {
    }

    public function __invoke(Article $article)
    {
    }
}
