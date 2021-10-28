<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Feature\Article\Action\ArticleCreateAction;
use App\Feature\Article\Action\ArticleDeleteAction;
use App\Feature\Article\Action\ArticleFavoriteAction;
use App\Feature\Article\Action\ArticleFeedAction;
use App\Feature\Article\Action\ArticleGetAction;
use App\Feature\Article\Action\ArticleListAction;
use App\Feature\Article\Action\ArticleUnfavoriteAction;
use App\Feature\Article\Action\ArticleUpdateAction;
use App\Feature\Article\Request\NewArticleRequest;
use App\Feature\Article\Request\UpdateArticleRequest;
use App\Feature\Article\Response\MultipleArticlesResponse;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'public.articles')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [
        'list' => [
            'method' => 'GET',
            'path' => '/articles',
            'controller' => ArticleListAction::class,
            'output' => MultipleArticlesResponse::class,
        ],
        'feed' => [
            'method' => 'GET',
            'path' => '/articles/feed',
            'controller' => ArticleFeedAction::class,
            'output' => MultipleArticlesResponse::class,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'create' => [
            'method' => 'POST',
            'path' => '/articles',
            'controller' => ArticleCreateAction::class,
            'input' => NewArticleRequest::class,
            'output' => SingleArticleResponse::class,
            'read' => false,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/articles/{slug}',
            'controller' => ArticleGetAction::class,
            'output' => SingleArticleResponse::class,
        ],
        'update' => [
            'method' => 'PUT',
            'path' => '/articles/{slug}',
            'controller' => ArticleUpdateAction::class,
            'input' => UpdateArticleRequest::class,
            'output' => SingleArticleResponse::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'delete' => [
            'method' => 'DELETE',
            'path' => '/articles/{slug}',
            'controller' => ArticleDeleteAction::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'favorite' => [
            'method' => 'POST',
            'status' => Response::HTTP_OK,
            'path' => '/articles/{slug}/favorite',
            'controller' => ArticleFavoriteAction::class,
            'output' => SingleArticleResponse::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'unfavorite' => [
            'method' => 'DELETE',
            'status' => Response::HTTP_OK,
            'path' => '/articles/{slug}/favorite',
            'controller' => ArticleUnfavoriteAction::class,
            'output' => SingleArticleResponse::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
    ]
)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public string $title;

    #[ORM\Column(type: 'text')]
    public string $description;

    #[ORM\Column(type: 'text')]
    public string $body;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[ApiProperty(identifier: true)]
    public ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    public User $author;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $updatedAt;

    /**
     * @var Collection|Tag[]
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'articles', cascade: ['persist'])]
    public $tags;

    /**
     * @var Collection|Comment[]
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', cascade: ['persist', 'remove'])]
    #[OrderBy(['id' => 'DESC'])]
    public $comments;

    /**
     * @var Collection|User[]
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteArticles', cascade: ['persist'])]
    public $favoritedBy;

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

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

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

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
