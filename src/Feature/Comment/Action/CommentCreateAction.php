<?php

namespace App\Feature\Comment\Action;

use App\Entity\Comment;
use App\Feature\Comment\Response\SingleCommentResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentCreateAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Comment $data)
    {
        $this->em->persist($data);
        $this->em->flush();

        return new SingleCommentResponse($data);
    }
}
