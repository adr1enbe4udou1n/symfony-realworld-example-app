<?php

namespace App\Feature\Article\Response;

use App\Entity\Article;
use App\Feature\Article\DTO\ArticleDTO;

class MultipleArticlesResponse
{
    /**
     * @var ArticleDTO[]|Article[]
     */
    public array $articles;
    public int $articlesCount;

    public function __construct(array $articles, int $articlesCount)
    {
        $this->articles = $articles;
        $this->articlesCount = $articlesCount;
    }
}
