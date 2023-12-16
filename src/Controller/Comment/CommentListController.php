<?php

namespace App\Controller\Comment;

use App\Dto\Comment\MultipleCommentsResponse;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentListController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
        private CommentRepository $comments,
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

        return $this->json(new MultipleCommentsResponse($this->comments->findByArticle($article), $user));
    }
}
