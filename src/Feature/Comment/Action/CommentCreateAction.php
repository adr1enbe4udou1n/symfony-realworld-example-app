<?php

namespace App\Feature\Comment\Action;

use App\Entity\Article;
use App\Feature\Comment\DTO\CommentDTO;
use App\Feature\Comment\Response\SingleCommentResponse;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentCreateAction extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
        private TagRepository $tags,
    ) {
    }

    public function __invoke(Article $article)
    {
        $response = new SingleCommentResponse();
        $response->comment = new CommentDTO();

        return $response;
    }
}
