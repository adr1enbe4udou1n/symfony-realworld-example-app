<?php

namespace App\Dto\Article;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateArticleDto
{
    #[Assert\NotBlank(allowNull: true)]
    public ?string $title = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $description = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $body = null;
}
