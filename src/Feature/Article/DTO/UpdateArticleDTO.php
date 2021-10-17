<?php

namespace App\Feature\Article\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateArticleDTO
{
    #[Assert\NotBlank(allowNull: true)]
    public string $title;

    #[Assert\NotBlank(allowNull: true)]
    public string $description;

    #[Assert\NotBlank(allowNull: true)]
    public string $body;
}
