<?php

namespace App\Feature\Article\ArgumentResolver;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private ArticleRepository $articles,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $request->attributes->has('slug') && 'article' === $argument->getName();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $article = $this->articles->findOneBy(['slug' => $request->attributes->get('slug')]);

        if (!$article) {
            throw new NotFoundHttpException('Not existing article');
        }

        yield $article;
    }
}
