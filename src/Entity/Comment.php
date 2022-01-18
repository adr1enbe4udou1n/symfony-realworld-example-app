<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Feature\Comment\Action\CommentCreateAction;
use App\Feature\Comment\Action\CommentDeleteAction;
use App\Feature\Comment\Action\CommentListAction;
use App\Feature\Comment\Request\NewCommentRequest;
use App\Feature\Comment\Response\MultipleCommentsResponse;
use App\Feature\Comment\Response\SingleCommentResponse;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [
        'list' => [
            'method' => 'GET',
            'path' => '/articles/{slug}/comments',
            'controller' => CommentListAction::class,
            'output' => MultipleCommentsResponse::class,
            'read' => false,
        ],
        'create' => [
            'method' => 'POST',
            'path' => '/articles/{slug}/comments',
            'controller' => CommentCreateAction::class,
            'input' => NewCommentRequest::class,
            'output' => SingleCommentResponse::class,
            'read' => false,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
    ],
    itemOperations: [
        'delete' => [
            'method' => 'DELETE',
            'path' => '/articles/{slug}/comments/{id}',
            'controller' => CommentDeleteAction::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
    ]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    public $id;

    #[ORM\Column(type: 'text')]
    public string $body;

    #[ORM\ManyToOne(targetEntity: Article::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    public Article $article;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
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
