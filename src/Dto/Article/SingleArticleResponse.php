<?php

namespace App\Dto\Article;

use App\Entity\Article;
use App\Entity\User;

class SingleArticleResponse
{
    /**
     * @var ArticleDto
     */
    public $article;

    public function __construct(Article $article, ?User $currentUser)
    {
        $this->article = new ArticleDto($article, $currentUser);
    }
}
