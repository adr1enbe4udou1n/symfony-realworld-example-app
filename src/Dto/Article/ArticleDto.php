<?php

namespace App\Dto\Article;

use App\Dto\Profile\ProfileDto;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ArticleDto
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

    public ProfileDto $author;

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
