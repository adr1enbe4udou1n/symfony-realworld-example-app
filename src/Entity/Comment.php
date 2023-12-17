<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
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
