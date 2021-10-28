<?php

namespace App\Feature\Article\Action;

use App\Feature\Article\Response\MultipleArticlesResponse;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ArticleListAction extends AbstractController
{
    public function __invoke(Request $request, ArticleRepository $articles)
    {
        $paginator = $articles->list(
            (int) $request->query->get('limit', (string) ArticleRepository::MAX_ITEMS_PER_PAGE),
            (int) $request->query->get('offset', '0'),
            $request->query->get('author'),
            $request->query->get('tag'),
            $request->query->get('favorited'),
        );

        return new MultipleArticlesResponse($paginator->getIterator()->getArrayCopy(), $paginator->count());
    }
}
