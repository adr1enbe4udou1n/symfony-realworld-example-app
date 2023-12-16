<?php

namespace App\Dto\Comment;

use Symfony\Component\Validator\Constraints as Assert;

class NewCommentDto
{
    #[Assert\NotBlank]
    public string $body;
}
