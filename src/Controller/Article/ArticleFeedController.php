<?php

namespace App\Controller\Article;

use App\Dto\Article\MultipleArticlesResponse;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ArticleFeedController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
    ) {
    }

    public function __invoke(Request $request)
    {
        /** @var User */
        $user = $this->getUser();

        $paginator = $this->articles->feed(
            $user,
            (int) $request->query->get('limit', (string) ArticleRepository::MAX_ITEMS_PER_PAGE),
            (int) $request->query->get('offset', '0'),
        );

        /** @var \ArrayObject */
        $iterator = $paginator->getIterator();

        return $this->json(new MultipleArticlesResponse(
            $iterator->getArrayCopy(),
            $paginator->count(),
            $user
        ));
    }
}
