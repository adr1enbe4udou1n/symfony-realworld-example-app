<?php

namespace App\Feature\Comment\Request;

use App\Feature\Comment\DTO\NewCommentDTO;
use Symfony\Component\Validator\Constraints\Valid;

class NewCommentRequest
{
    #[Valid]
    public NewCommentDTO $comment;
}
