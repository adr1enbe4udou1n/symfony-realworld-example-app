<?php

namespace App\Feature\Comment\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class NewCommentDTO
{
    #[Assert\NotBlank]
    public string $body;
}
