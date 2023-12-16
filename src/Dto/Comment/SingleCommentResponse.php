<?php

namespace App\Dto\Comment;

use App\Entity\Comment;
use App\Entity\User;

class SingleCommentResponse
{
    /**
     * @var CommentDto
     */
    public $comment;

    public function __construct(Comment $comment, ?User $currentUser)
    {
        $this->comment = new CommentDto($comment, $currentUser);
    }
}
