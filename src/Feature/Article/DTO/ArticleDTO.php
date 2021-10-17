<?php

namespace App\Feature\Article\DTO;

use App\Feature\Profile\DTO\ProfileDTO;

class ArticleDTO
{
    public string $title;

    public string $slug;

    public string $description;

    public string $body;

    public \DateTime $createdAt;

    public \DateTime $updatedAt;

    /**
     * @var string[]
     */
    public array $tagList = [];

    public ProfileDTO $author;

    public bool $favorited;

    public int $favoritesCount;
}
