<?php

namespace App\Feature\Article\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Feature\Article\Request\NewArticleRequest;
use App\Repository\TagRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class NewArticleDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private TokenStorageInterface $token,
        private TagRepository $tags,
    ) {
    }

    /**
     * @param NewArticleRequest $data
     */
    public function transform($data, string $to, array $context = []): Article
    {
        $this->validator->validate($data->article);

        /** @var User */
        $user = $this->token->getToken()->getUser();

        $article = new Article();
        $article->title = $data->article->title;
        $article->description = $data->article->description;
        $article->body = $data->article->body;
        $article->author = $user;

        $existingTags = $this->tags->byNames($data->article->tagList);

        foreach ($data->article->tagList as $tagName) {
            $tags = array_filter($existingTags, fn (Tag $t) => $t->name === $tagName);

            if (empty($tags)) {
                $article->addTag((new Tag())->setName($tagName));
                continue;
            }

            $article->addTag(reset($tags));
        }

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

        return NewArticleRequest::class === ($context['input']['class'] ?? null);
    }
}
