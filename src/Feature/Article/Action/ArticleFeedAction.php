<?php

namespace App\Feature\Article\Action;

use App\Entity\User;
use App\Feature\Article\Response\MultipleArticlesResponse;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleFeedAction extends AbstractController
{
    public function __invoke(Request $request, ArticleRepository $articles, TokenStorageInterface $token)
    {
        /** @var User */
        $user = $token->getToken()->getUser();

        $paginator = $articles->feed(
            $user,
            (int) $request->query->get('limit', (string) ArticleRepository::MAX_ITEMS_PER_PAGE),
            (int) $request->query->get('offset', '0'),
        );

        return new MultipleArticlesResponse($paginator->getQuery()->getResult(), $paginator->count());
    }
}
