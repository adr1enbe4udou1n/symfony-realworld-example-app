<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\Comment\CommentCreateController;
use App\Controller\Comment\CommentDeleteController;
use App\Controller\Comment\CommentListController;
use App\Dto\Comment\MultipleCommentsResponse;
use App\Dto\Comment\NewCommentRequest;
use App\Dto\Comment\SingleCommentResponse;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Get(
    name: 'GetArticleComments',
    uriTemplate: '/articles/{slug}/comments',
    controller: CommentListController::class,
    output: MultipleCommentsResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get comments for an article.',
        description: 'Get the comments for an article. Auth is optional',
        tags: ['Comments'],
        security: [],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to get comments for'
            ),
        ],
    )
)]
#[Post(
    name: 'CreateArticleComment',
    uriTemplate: '/articles/{slug}/comments',
    controller: CommentCreateController::class,
    security: 'is_granted("ROLE_USER")',
    input: NewCommentRequest::class,
    output: SingleCommentResponse::class,
    openapi: new Operation(
        summary: 'Create a comment for an article.',
        description: 'Create a comment for an article. Auth is required',
        tags: ['Comments'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to create a comment for'
            ),
        ],
    )
)]
#[Delete(
    name: 'DeleteArticleComment',
    uriTemplate: '/articles/{slug}/comments/{commentId}',
    controller: CommentDeleteController::class,
    security: 'is_granted("ROLE_USER")',
    output: false,
    read: false,
    openapi: new Operation(
        summary: 'Delete a comment for an article.',
        description: 'Delete a comment for an article. Auth is required',
        tags: ['Comments'],
        parameters: [
            new Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Slug of the article that you want to delete a comment for'
            ),
            new Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Id of the comment that you want to delete'
            ),
        ],
    )
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    public $id;

    #[ORM\Column(type: 'text')]
    public string $body;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    public Article $article;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    public User $author;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $updatedAt;

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
