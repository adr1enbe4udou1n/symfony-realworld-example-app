<?php

namespace App\Feature\Comment\Response;

use App\Entity\Comment;
use App\Entity\User;
use App\Feature\Comment\DTO\CommentDTO;

class SingleCommentResponse
{
    /**
     * @var CommentDTO
     */
    public $comment;

    public static function make(Comment $comment, ?User $currentUser)
    {
        $response = new self();
        $response->comment = new CommentDTO($comment, $currentUser);

        return $response;
    }
}
