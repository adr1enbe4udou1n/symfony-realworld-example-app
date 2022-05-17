<?php

namespace App\Feature\Article\Response;

use App\Entity\Article;
use App\Entity\User;
use App\Feature\Article\DTO\ArticleDTO;

class MultipleArticlesResponse
{
    /**
     * @var array<ArticleDTO>
     */
    public array $articles;
    public int $articlesCount;

    /**
     * @param array<Article> $articles
     */
    public static function make(array $articles, int $articlesCount, ?User $currentUser)
    {
        $response = new self();

        $response->articles = array_map(fn (Article $article) => new ArticleDTO($article, $currentUser), $articles);
        $response->articlesCount = $articlesCount;

        return $response;
    }
}
