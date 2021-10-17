<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Feature\Comment\Action\CommentCreateAction;
use App\Feature\Comment\Action\CommentDeleteAction;
use App\Feature\Comment\Request\NewCommentRequest;
use App\Feature\Comment\Response\SingleCommentResponse;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'public.comments')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
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
        'delete' => [
            'method' => 'DELETE',
            'path' => '/articles/{slug}/comments/{id}',
            'controller' => CommentDeleteAction::class,
            'read' => false,
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
    public $id;

    #[ORM\Column(type: 'text')]
    public string $body;

    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(nullable: false)]
    public Article $article;

    #[ORM\ManyToOne(targetEntity: User::class)]
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
