<?php

namespace App\Dto\Article;

use App\Entity\Article;
use App\Entity\User;

class MultipleArticlesResponse
{
    /**
     * @var array<ArticleDto>
     */
    public array $articles;
    public int $articlesCount;

    /**
     * @param array<Article> $articles
     */
    public function __construct(array $articles, int $articlesCount, ?User $currentUser)
    {
        $this->articles = array_map(fn (Article $article) => new ArticleDto($article, $currentUser), $articles);
        $this->articlesCount = $articlesCount;
    }
}
