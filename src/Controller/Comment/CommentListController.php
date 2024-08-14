<?php

namespace App\Controller\Comment;

use App\Dto\Comment\MultipleCommentsResponse;
use App\Entity\Article;
use App\Entity\User;
use App\Repository\CommentRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentListController extends AbstractController
{
    public function __construct(
        private CommentRepository $comments,
    ) {
    }

    public function __invoke(#[MapEntity(mapping: ['slug' => 'slug'])] Article $article)
    {
        /** @var User */
        $user = $this->getUser();

        return $this->json(new MultipleCommentsResponse($this->comments->findByArticle($article), $user));
    }
}
