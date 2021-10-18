<?php

namespace App\Feature\Comment\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Comment;
use App\Feature\Comment\DTO\CommentDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CommentDataTransformer implements DataTransformerInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    /**
     * @param Comment $data
     */
    public function transform($data, string $to, array $context = []): CommentDTO
    {
        $comment = new CommentDTO();

        $comment->id = $data->id;
        $comment->body = $data->body;
        $comment->createdAt = $data->createdAt;
        $comment->updatedAt = $data->updatedAt;

        $comment->author = $data->author->getProfile($this->token);

        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Comment;
    }
}
