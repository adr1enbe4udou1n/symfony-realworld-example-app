<?php

namespace App\Feature\Comment\Action;

use App\Feature\Comment\Response\MultipleCommentsResponse;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentListAction extends AbstractController
{
    public function __invoke(ArticleRepository $articles, Request $request)
    {
        $article = $articles->findOneBy(['slug' => $request->attributes->get('slug')]);

        if (!$article) {
            return new JsonResponse('No article of this slug found', 404);
        }

        return new MultipleCommentsResponse($article->comments->toArray());
    }
}
