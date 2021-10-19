<?php

namespace App\Feature\Comment\Action;

use App\Entity\Comment;
use App\Feature\Comment\Response\SingleCommentResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentCreateAction extends AbstractController
{
    public function __invoke(Comment $data, EntityManagerInterface $em)
    {
        $em->persist($data);
        $em->flush();

        return new SingleCommentResponse($data);
    }
}
