<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\Profile\ProfileFollowController;
use App\Controller\Profile\ProfileGetController;
use App\Controller\Profile\ProfileUnfollowController;
use App\Controller\User\LoginController;
use App\Controller\User\RegisterController;
use App\Controller\User\UserGetController;
use App\Controller\User\UserUpdateController;
use App\Dto\Profile\ProfileDto;
use App\Dto\Profile\ProfileResponse;
use App\Dto\User\LoginUserRequest;
use App\Dto\User\NewUserRequest;
use App\Dto\User\UpdateUserRequest;
use App\Dto\User\UserResponse;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'user.email.unique')]
#[ORM\HasLifecycleCallbacks]
#[Post(
    name: 'CreateUser',
    uriTemplate: '/users',
    controller: RegisterController::class,
    input: NewUserRequest::class,
    output: UserResponse::class,
    validationContext: ['groups' => [NewUserRequest::class, 'validationGroups']],
    openapi: new Operation(
        summary: 'Register a new user.',
        description: 'Register a new user',
        tags: ['User and Authentication'],
        security: [],
    )
)]
#[Post(
    name: 'Login',
    uriTemplate: '/users/login',
    controller: LoginController::class,
    input: LoginUserRequest::class,
    output: UserResponse::class,
    openapi: new Operation(
        summary: 'Existing user login.',
        description: 'Login for existing user',
        tags: ['User and Authentication'],
        security: [],
    )
)]
#[Get(
    name: 'GetCurrentUser',
    uriTemplate: '/user',
    controller: UserGetController::class,
    output: UserResponse::class,
    read: false,
    security: 'is_granted("ROLE_USER")',
    openapi: new Operation(
        summary: 'Get current user.',
        description: 'Gets the currently logged-in user',
        tags: ['User and Authentication'],
        security: [['Bearer' => []]],
    )
)]
#[Put(
    name: 'UpdateCurrentUser',
    uriTemplate: '/user',
    controller: UserUpdateController::class,
    input: UpdateUserRequest::class,
    output: UserResponse::class,
    read: false,
    security: 'is_granted("ROLE_USER")',
    openapi: new Operation(
        summary: 'Update current user.',
        description: 'Updated user information for current user',
        tags: ['User and Authentication'],
    )
)]
#[Get(
    name: 'GetProfileByUsername',
    uriTemplate: '/profiles/{username}',
    controller: ProfileGetController::class,
    output: ProfileResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get a profile.',
        description: 'Get a profile of a user of the system. Auth is optional',
        tags: ['Profile'],
        security: [],
        parameters: [
            new Parameter(
                name: 'username',
                in: 'path',
                required: true,
                description: 'Username of the profile to get',
            ),
        ],
    )
)]
#[Post(
    name: 'FollowUserByUsername',
    uriTemplate: '/profiles/{username}/follow',
    controller: ProfileFollowController::class,
    security: 'is_granted("ROLE_USER")',
    input: false,
    output: ProfileResponse::class,
    openapi: new Operation(
        summary: 'Follow a user.',
        description: 'Follow a user by username',
        tags: ['Profile'],
        parameters: [
            new Parameter(
                name: 'username',
                in: 'path',
                required: true,
                description: 'Username of the profile you want to follow',
            ),
        ],
    )
)]
#[Delete(
    name: 'UnfollowUserByUsername',
    uriTemplate: '/profiles/{username}/follow',
    controller: ProfileUnfollowController::class,
    security: 'is_granted("ROLE_USER")',
    output: ProfileResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Unfollow a user.',
        description: 'Unfollow a user by username',
        tags: ['Profile'],
        parameters: [
            new Parameter(
                name: 'username',
                in: 'path',
                required: true,
                description: 'Username of the profile you want to unfollow',
            ),
        ],
    )
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    public ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
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
    public $following;

    /**
     * @var Collection|User[]
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'follower_user')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    public $followers;

    /**
     * @var Collection|Article[]
     */
    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'favoritedBy', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'article_favorite')]
    public $favoriteArticles;

    /**
     * @var Collection|Article[]
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author')]
    public $articles;

    /**
     * @var Collection|Comment[]
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    public $comments;

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

    public function setFollowers(array $data): self
    {
        $this->followers = new ArrayCollection($data);

        return $this;
    }

    public function setFavoriteArticles(array $data): self
    {
        $this->favoriteArticles = new ArrayCollection($data);

        return $this;
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

    public function getProfile(?User $currentUser): ProfileDto
    {
        $profile = new ProfileDto();
        $profile->username = $this->name;
        $profile->bio = $this->bio ?? '';
        $profile->image = $this->image ?? '';

        if ($currentUser) {
            $profile->following = $currentUser->following->contains($this);
        }

        return $profile;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
