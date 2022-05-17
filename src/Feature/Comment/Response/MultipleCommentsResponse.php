<?php

namespace App\Feature\Comment\Response;

use App\Entity\Comment;
use App\Feature\Comment\DTO\CommentDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MultipleCommentsResponse
{
    /**
     * @var array<CommentDTO>
     */
    public array $comments;

    /**
     * @param array<Comment> $comments
     */
    public static function make(array $comments, TokenStorageInterface $token)
    {
        $response = new self();
        $response->comments = array_map(fn (Comment $comment) => new CommentDTO($comment, $token), $comments);

        return $response;
    }
}
