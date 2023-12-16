<?php

namespace App\Dto\Article;

use Symfony\Component\Validator\Constraints\Valid;

class NewArticleRequest
{
    #[Valid]
    public NewArticleDto $article;
}
