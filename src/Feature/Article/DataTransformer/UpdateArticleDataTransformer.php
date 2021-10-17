<?php

namespace App\Feature\Article\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Article;
use App\Feature\Article\Request\UpdateArticleRequest;

final class UpdateArticleDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param UpdateArticleRequest $data
     */
    public function transform($data, string $to, array $context = []): Article
    {
        $this->validator->validate($data->article);

        $article = new Article();
        $article->title = $data->article->title;
        $article->description = $data->article->description;
        $article->body = $data->article->body;

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
