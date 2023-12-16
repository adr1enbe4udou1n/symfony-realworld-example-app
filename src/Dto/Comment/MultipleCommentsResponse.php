<?php

namespace App\Dto\Comment;

use App\Entity\Comment;
use App\Entity\User;

class MultipleCommentsResponse
{
    /**
     * @var array<CommentDto>
     */
    public array $comments;

    /**
     * @param array<Comment> $comments
     */
    public function __construct(array $comments, ?User $currentUser)
    {
        $this->comments = array_map(fn (Comment $comment) => new CommentDto($comment, $currentUser), $comments);
    }
}
