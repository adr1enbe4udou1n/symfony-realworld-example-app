<?php

namespace App\Feature\Article\Request;

use App\Feature\Article\DTO\NewArticleDTO;
use Symfony\Component\Validator\Constraints\Valid;

class NewArticleRequest
{
    #[Valid]
    public NewArticleDTO $article;
}
