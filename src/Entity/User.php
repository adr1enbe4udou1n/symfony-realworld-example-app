<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Feature\Profile\Action\ProfileFollowAction;
use App\Feature\Profile\Action\ProfileGetAction;
use App\Feature\Profile\Action\ProfileUnfollowAction;
use App\Feature\Profile\DTO\ProfileDTO;
use App\Feature\Profile\Response\ProfileResponse;
use App\Feature\User\Action\CurrentUserAction;
use App\Feature\User\Action\LoginUserAction;
use App\Feature\User\Action\RegisterUserAction;
use App\Feature\User\Action\UpdateUserAction;
use App\Feature\User\Request\LoginUserRequest;
use App\Feature\User\Request\NewUserRequest;
use App\Feature\User\Request\UpdateUserRequest;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'public.users')]
#[UniqueEntity('email', message: 'user.email.unique')]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'register' => [
            'method' => 'POST',
            'status' => Response::HTTP_OK,
            'path' => '/users',
            'controller' => RegisterUserAction::class,
            'input' => NewUserRequest::class,
            'output' => UserResponse::class,
            'read' => false,
            'write' => false,
        ],
        'login' => [
            'method' => 'POST',
            'status' => Response::HTTP_OK,
            'path' => '/users/login',
            'controller' => LoginUserAction::class,
            'input' => LoginUserRequest::class,
            'output' => UserResponse::class,
            'read' => false,
            'write' => false,
        ],
        'current' => [
            'method' => 'GET',
            'path' => '/user',
            'controller' => CurrentUserAction::class,
            'output' => UserResponse::class,
            'read' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'update' => [
            'method' => 'PUT',
            'path' => '/user',
            'controller' => UpdateUserAction::class,
            'input' => UpdateUserRequest::class,
            'output' => UserResponse::class,
            'read' => false,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'profile' => [
            'method' => 'GET',
            'path' => '/profiles/celeb_{name}',
            'controller' => ProfileGetAction::class,
            'output' => ProfileResponse::class,
        ],
        'follow' => [
            'method' => 'POST',
            'status' => Response::HTTP_OK,
            'path' => '/profiles/celeb_{name}/follow',
            'controller' => ProfileFollowAction::class,
            'output' => ProfileResponse::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'unfollow' => [
            'method' => 'DELETE',
            'status' => Response::HTTP_OK,
            'path' => '/profiles/celeb_{name}/follow',
            'controller' => ProfileUnfollowAction::class,
            'output' => ProfileResponse::class,
            'write' => false,
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
    ],
)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    public ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[ApiProperty(identifier: true)]
    public string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public string $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $password = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $bio = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $image = null;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $updatedAt;

    /**
     * @var Collection|User[]
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers', cascade: ['persist'])]
    public Collection $following;

    /**
     * @var Collection|User[]
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'follower_user')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    public Collection $followers;

    /**
     * @var Collection|Article[]
     */
    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'favoritedBy', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'article_favorite')]
    public Collection $favoriteArticles;

    /**
     * @var Collection|Article[]
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author')]
    public Collection $articles;

    /**
     * @var Collection|Comment[]
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    public Collection $comments;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->favoriteArticles = new ArrayCollection();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
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

    public function follow(User $user): self
    {
        if (!$this->followers->contains($this)) {
            $user->followers[] = $this;
        }

        return $this;
    }

    public function unfollow(User $user): self
    {
        $user->followers->removeElement($this);

        return $this;
    }

    public function favorite(Article $article): self
    {
        if (!$this->favoriteArticles->contains($article)) {
            $this->favoriteArticles[] = $article;
        }

        return $this;
    }

    public function unfavorite(Article $article): self
    {
        $this->favoriteArticles->removeElement($article);

        return $this;
    }

    public function getProfile(TokenStorageInterface $token): ProfileDTO
    {
        $profile = new ProfileDTO();
        $profile->username = $this->name;
        $profile->bio = $this->bio;
        $profile->image = $this->image;

        if ($token = $token->getToken()) {
            /** @var User */
            $user = $token->getUser();
            $profile->following = $user->following->contains($this);
        }

        return $profile;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
