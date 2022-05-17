<?php

namespace App\Feature\Comment\Response;

use App\Entity\Comment;
use App\Feature\Comment\DTO\CommentDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SingleCommentResponse
{
    /**
     * @var CommentDTO
     */
    public $comment;

    public static function make(Comment $comment, TokenStorageInterface $token)
    {
        $response = new self();
        $response->comment = new CommentDTO($comment, $token);

        return $response;
    }
}
