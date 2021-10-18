<?php

namespace App\Feature\Article\Response;

use App\Entity\Article;
use App\Feature\Article\DTO\ArticleDTO;

class SingleArticleResponse
{
    /**
     * @var ArticleDTO|Article
     */
    public $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }
}
