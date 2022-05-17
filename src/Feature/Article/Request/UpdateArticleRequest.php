<?php

namespace App\Feature\Article\Request;

use App\Feature\Article\DTO\UpdateArticleDTO;
use Symfony\Component\Validator\Constraints\Valid;

class UpdateArticleRequest
{
    #[Valid]
    public UpdateArticleDTO $article;
}
