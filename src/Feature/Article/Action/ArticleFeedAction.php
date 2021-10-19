<?php

namespace App\Feature\Article\Action;

use App\Feature\Article\Response\MultipleArticlesResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleFeedAction extends AbstractController
{
    public function __invoke()
    {
        return new MultipleArticlesResponse([], 0);
    }
}
