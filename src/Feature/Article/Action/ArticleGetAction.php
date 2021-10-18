<?php

namespace App\Feature\Article\Action;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleGetAction extends AbstractController
{
    public function __invoke(Article $data)
    {
        return $data;
    }
}
