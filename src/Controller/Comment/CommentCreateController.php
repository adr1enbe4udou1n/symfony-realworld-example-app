<?php

namespace App\Controller\Comment;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Comment\NewCommentRequest;
use App\Dto\Comment\SingleCommentResponse;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentCreateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles
    ) {
    }

    public function __invoke(Article $article, NewCommentRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

        /** @var User */
        $user = $this->getUser();

        $comment = new Comment();
        $comment->body = $data->comment->body;
        $comment->author = $user;
        $comment->article = $article;

        $this->em->persist($comment);
        $this->em->flush();

        return $this->json(new SingleCommentResponse($comment, $user));
    }
}
