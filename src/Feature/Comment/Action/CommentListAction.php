<?php

namespace App\Feature\Comment\Action;

use App\Feature\Comment\Response\MultipleCommentsResponse;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentListAction extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
        private RequestStack $request,
    ) {
    }

    public function __invoke()
    {
        $article = $this->articles->findOneBy(['slug' => $this->request->getCurrentRequest()->attributes->get('slug')]);

        if (!$article) {
            return new JsonResponse('No article of this slug found', 404);
        }

        return new MultipleCommentsResponse($article->comments->toArray());
    }
}
