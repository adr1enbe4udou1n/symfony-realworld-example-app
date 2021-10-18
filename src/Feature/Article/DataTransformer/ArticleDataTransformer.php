<?php

namespace App\Feature\Article\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Feature\Article\DTO\ArticleDTO;
use App\Feature\Article\Response\SingleArticleResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ArticleDataTransformer implements DataTransformerInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    /**
     * @param Article $data
     */
    public function transform($data, string $to, array $context = []): ArticleDTO
    {
        $article = new ArticleDTO();

        $article->title = $data->title;
        $article->description = $data->description;
        $article->body = $data->body;
        $article->createdAt = $data->createdAt;
        $article->updatedAt = $data->updatedAt;

        /** @var User */
        $user = $this->token->getToken()->getUser();

        $article->author = $data->author->getProfile($this->token);
        $article->tagList = $data->tags->map(fn (Tag $t) => $t->name)->toArray();
        $article->favorited = $data->favoritedBy->contains($user);
        $article->favoritesCount = $data->favoritedBy->count();

        return $article;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return SingleArticleResponse::class === $to && $data instanceof Article;
    }
}
