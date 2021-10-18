<?php

namespace App\Feature\Article\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Article;
use App\Repository\ArticleRepository;

final class ArticleItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private ArticleRepository $articles,
    ) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Article::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $slug, string $operationName = null, array $context = []): ?Article
    {
        return $this->articles->findOneBy(['slug' => $slug]);
    }
}
