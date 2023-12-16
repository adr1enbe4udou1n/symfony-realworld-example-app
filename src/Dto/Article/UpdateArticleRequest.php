<?php

namespace App\Dto\Article;

use Symfony\Component\Validator\Constraints\Valid;

class UpdateArticleRequest
{
    #[Valid]
    public UpdateArticleDto $article;
}
