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
    public function transform($data, string $to, array $context = []): SingleArticleResponse
    {
        $output = new SingleArticleResponse();
        $output->article = new ArticleDTO();

        $output->article->title = $data->title;
        $output->article->description = $data->description;
        $output->article->body = $data->body;
        $output->article->createdAt = $data->createdAt;
        $output->article->updatedAt = $data->updatedAt;

        /** @var User */
        $user = $this->token->getToken()->getUser();

        $output->article->author = $data->author->getProfile($this->token);
        $output->article->tagList = $data->tags->map(fn (Tag $t) => $t->name)->toArray();
        $output->article->favorited = $data->favoritedBy->contains($user);
        $output->article->favoritesCount = $data->favoritedBy->count();

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return SingleArticleResponse::class === $to && $data instanceof Article;
    }
}
