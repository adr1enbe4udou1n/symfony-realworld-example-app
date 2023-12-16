<?php

namespace App\Controller\Comment;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Comment\NewCommentRequest;
use App\Dto\Comment\SingleCommentResponse;
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

    public function __invoke($slug, NewCommentRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

        $article = $this->articles->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException();
        }

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
