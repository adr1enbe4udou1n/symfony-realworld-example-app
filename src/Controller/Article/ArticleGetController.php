<?php

namespace App\Controller\Article;

use App\Dto\Article\SingleArticleResponse;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleGetController extends AbstractController
{
    public function __invoke(#[MapEntity(mapping: ['slug'])] Article $article)
    {
        /** @var User */
        $user = $this->getUser();

        return $this->json(new SingleArticleResponse($article, $user));
    }
}
