<?php

namespace App\Feature\Article\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Article;
use App\Feature\Article\Request\UpdateArticleRequest;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateArticleDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ArticleRepository $articles,
        private ValidatorInterface $validator,
        private RequestStack $request,
    ) {
    }

    /**
     * @param UpdateArticleRequest $data
     */
    public function transform($data, string $to, array $context = []): Article
    {
        $article = $this->articles->findOneBy(['slug' => $this->request->getCurrentRequest()->attributes->get('slug')]);

        if (!$article) {
            throw new NotFoundHttpException('Not existing article');
        }

        $this->validator->validate($data->article);

        $article->title = $data->article->title ?? $article->title;
        $article->description = $data->article->description ?? $article->description;
        $article->body = $data->article->body ?? $article->body;

        return $article;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Article) {
            return false;
        }

        return UpdateArticleRequest::class === ($context['input']['class'] ?? null);
    }
}
