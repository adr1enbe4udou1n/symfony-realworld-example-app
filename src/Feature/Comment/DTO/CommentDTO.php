<?php

namespace App\Feature\Comment\DTO;

use App\Entity\Comment;
use App\Feature\Profile\DTO\ProfileDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class CommentDTO
{
    public int $id;

    public string $body;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.u\Z'])]
    public \DateTime $createdAt;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.u\Z'])]
    public \DateTime $updatedAt;

    public ProfileDTO $author;

    public function __construct(Comment $comment, TokenStorageInterface $token)
    {
        $this->id = $comment->id;
        $this->body = $comment->body;
        $this->createdAt = $comment->createdAt;
        $this->updatedAt = $comment->updatedAt;
        $this->author = $comment->author->getProfile($token);
    }
}
