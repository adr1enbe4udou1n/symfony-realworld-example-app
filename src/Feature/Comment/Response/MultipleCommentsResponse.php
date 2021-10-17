<?php

namespace App\Feature\Comment\Response;

use App\Feature\Comment\DTO\CommentDTO;

class MultipleCommentsResponse
{
    /**
     * @var CommentDTO[]
     */
    public array $comments;
}
