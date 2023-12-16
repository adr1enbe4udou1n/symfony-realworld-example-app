<?php

namespace App\Dto\Comment;

use Symfony\Component\Validator\Constraints\Valid;

class NewCommentRequest
{
    #[Valid]
    public NewCommentDto $comment;
}
