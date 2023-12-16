<?php

namespace App\Dto\Article;

use Symfony\Component\Validator\Constraints as Assert;

class NewArticleDto
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
