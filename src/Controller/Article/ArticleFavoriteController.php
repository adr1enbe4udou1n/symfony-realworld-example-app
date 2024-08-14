<?php

namespace App\Controller\Article;

use App\Dto\Article\SingleArticleResponse;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleFavoriteController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(#[MapEntity(mapping: ['slug' => 'slug'])] Article $article)
    {
        /** @var User */
        $user = $this->getUser();

        $user->favorite($article);
        $this->em->flush();

        return $this->json(new SingleArticleResponse($article, $user));
    }
}
