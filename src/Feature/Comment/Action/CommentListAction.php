<?php

namespace App\Feature\Comment\Action;

use App\Entity\Article;
use App\Feature\Comment\Response\MultipleCommentsResponse;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentListAction extends AbstractController
{
    public function __construct(
        private CommentRepository $comments,
    ) {
    }

    public function __invoke(Article $article)
    {
        $response = new MultipleCommentsResponse();
        $response->comments = [];
        // $response->comments = $this->comments->findBy(['article_id' => $article->id]);

        return $response;
    }
}
