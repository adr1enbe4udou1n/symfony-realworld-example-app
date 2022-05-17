<?php

namespace App\Feature\Article\Response;

use App\Entity\Article;
use App\Feature\Article\DTO\ArticleDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SingleArticleResponse
{
    public ArticleDTO $article;

    public static function make(Article $article, TokenStorageInterface $token)
    {
        $response = new self();
        $response->article = new ArticleDTO($article, $token);

        return $response;
    }
}
