<?php

namespace App\Feature\Article\DTO;

use App\Feature\Profile\DTO\ProfileDTO;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ArticleDTO
{
    public string $title;

    public string $slug;

    public string $description;

    public string $body;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.u\Z'])]
    public \DateTime $createdAt;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.u\Z'])]
    public \DateTime $updatedAt;

    /**
     * @var string[]
     */
    public array $tagList = [];

    public ProfileDTO $author;

    public bool $favorited = false;

    public int $favoritesCount = 0;
}
