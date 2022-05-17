<?php

namespace App\Feature\Article\Response;

use App\Entity\Article;
use App\Feature\Article\DTO\ArticleDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    public static function make(array $articles, int $articlesCount, TokenStorageInterface $token)
    {
        $response = new self();

        $response->articles = array_map(fn (Article $article) => new ArticleDTO($article, $token), $articles);
        $response->articlesCount = $articlesCount;

        return $response;
    }
}
