<?php

namespace App\Feature\Comment\Response;

use App\Entity\Comment;
use App\Feature\Comment\DTO\CommentDTO;

class SingleCommentResponse
{
    /**
     * @var CommentDTO|Comment
     */
    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}
