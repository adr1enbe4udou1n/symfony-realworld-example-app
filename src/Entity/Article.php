<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'public.articles')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [],
    itemOperations: []
)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public string $title;

    #[ORM\Column(type: 'text')]
    public string $description;

    #[ORM\Column(type: 'text')]
    public string $body;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public string $slug;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public User $author;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $updatedAt;

    /**
     * @var Collection|Tag[]
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'articles')]
    public Collection $tags;

    /**
     * @var Collection|Comment[]
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', cascade: ['remove'])]
    public Collection $comments;

    /**
     * @var Collection|User[]
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteArticles')]
    public Collection $favoritedBy;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->favoritedBy = new ArrayCollection();
    }

    public function computeSlug(SluggerInterface $slugger)
    {
        if (!$this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
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

    public function __toString(): string
    {
        return $this->title;
    }
}
