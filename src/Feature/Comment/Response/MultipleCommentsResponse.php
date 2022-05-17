<?php

namespace App\Feature\Comment\Response;

use App\Entity\Comment;
use App\Entity\User;
use App\Feature\Comment\DTO\CommentDTO;

class MultipleCommentsResponse
{
    /**
     * @var array<CommentDTO>
     */
    public array $comments;

    /**
     * @param array<Comment> $comments
     */
    public static function make(array $comments, User $currentUser)
    {
        $response = new self();
        $response->comments = array_map(fn (Comment $comment) => new CommentDTO($comment, $currentUser), $comments);

        return $response;
    }
}
