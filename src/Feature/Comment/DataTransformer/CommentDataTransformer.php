<?php

namespace App\Feature\Comment\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Comment;
use App\Feature\Comment\DTO\CommentDTO;
use App\Feature\Comment\Response\SingleCommentResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CommentDataTransformer implements DataTransformerInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    /**
     * @param Comment $data
     */
    public function transform($data, string $to, array $context = []): SingleCommentResponse
    {
        $output = new SingleCommentResponse();
        $output->comment = new CommentDTO();

        $output->comment->id = $data->id;
        $output->comment->body = $data->body;
        $output->comment->createdAt = $data->createdAt;
        $output->comment->updatedAt = $data->updatedAt;

        $output->comment->author = $data->author->getProfile($this->token);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return SingleCommentResponse::class === $to && $data instanceof Comment;
    }
}
