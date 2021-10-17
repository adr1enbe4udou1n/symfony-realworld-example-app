<?php

namespace App\Feature\Article\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class NewArticleDTO
{
    #[Assert\NotBlank]
    public string $title;

    #[Assert\NotBlank]
    public string $description;

    #[Assert\NotBlank]
    public string $body;

    /**
     * @var string[]
     */
    public array $tagList = [];
}
