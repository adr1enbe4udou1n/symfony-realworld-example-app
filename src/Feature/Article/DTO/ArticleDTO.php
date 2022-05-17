<?php

namespace App\Feature\Article\DTO;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
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

    public function __construct(Article $article, ?User $currentUser)
    {
        $this->title = $article->title;
        $this->slug = $article->slug;
        $this->description = $article->description;
        $this->body = $article->body;
        $this->createdAt = $article->createdAt;
        $this->updatedAt = $article->updatedAt;
        $this->author = $article->author->getProfile($currentUser);

        $tags = $article->tags->map(fn (Tag $t) => $t->name)->toArray();
        sort($tags);
        $this->tagList = $tags;

        if ($currentUser) {
            $this->favorited = $article->favoritedBy->contains($currentUser);
        }

        $this->favoritesCount = $article->favoritedBy->count();
    }
}
