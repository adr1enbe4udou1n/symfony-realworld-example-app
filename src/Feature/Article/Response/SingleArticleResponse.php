<?php

namespace App\Feature\Article\Response;

use App\Entity\Article;
use App\Entity\User;
use App\Feature\Article\DTO\ArticleDTO;

class SingleArticleResponse
{
    public ArticleDTO $article;

    public static function make(Article $article, ?User $currentUser)
    {
        $response = new self();
        $response->article = new ArticleDTO($article, $currentUser);

        return $response;
    }
}
