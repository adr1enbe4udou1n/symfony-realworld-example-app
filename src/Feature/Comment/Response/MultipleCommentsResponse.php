<?php

namespace App\Feature\Comment\Response;

use App\Entity\Comment;
use App\Feature\Comment\DTO\CommentDTO;

class MultipleCommentsResponse
{
    /**
     * @var CommentDTO[]|Comment[]
     */
    public array $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }
}
