<?php

namespace App\Feature\Article\Response;

use App\Feature\Article\DTO\ArticleDTO;

class MultipleArticlesResponse
{
    /**
     * @var ArticleDTO[]
     */
    public array $articles;
    public int $articlesCount;
}
