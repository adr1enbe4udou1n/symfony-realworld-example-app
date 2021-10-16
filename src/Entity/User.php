<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Feature\Profile\Action\ProfileFollowAction;
use App\Feature\Profile\Action\ProfileGetAction;
use App\Feature\Profile\Action\ProfileUnfollowAction;
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
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'public.user')]
#[UniqueEntity('email', message: 'user.email.unique')]
#[ApiResource(
    collectionOperations: [
        'register' => [
            'method' => 'POST',
            'path' => '/users',
            'controller' => RegisterUserAction::class,
            'input' => NewUserRequest::class,
            'output' => UserResponse::class,
            'read' => false,
            'write' => false,
            'openapi_context' => [
                'summary' => 'Register a new user',
                'description' => 'Register a new user',
            ],
        ],
        'login' => [
            'method' => 'POST',
            'path' => '/users/login',
            'controller' => LoginUserAction::class,
            'input' => LoginUserRequest::class,
            'output' => UserResponse::class,
            'read' => false,
            'write' => false,
            'openapi_context' => [
                'summary' => 'Existing user login',
                'description' => 'Login for existing user',
            ],
        ],
    ],
    itemOperations: [
        'current' => [
            'method' => 'GET',
            'path' => '/user',
            'controller' => CurrentUserAction::class,
            'read' => false,
            'write' => false,
            'output' => UserResponse::class,
            'openapi_context' => [
                'summary' => 'Get current user',
                'description' => 'Gets the currently logged-in user',
            ],
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'update' => [
            'method' => 'PUT',
            'path' => '/user',
            'controller' => UpdateUserAction::class,
            'input' => UpdateUserRequest::class,
            'read' => false,
            'write' => false,
            'output' => UserResponse::class,
            'openapi_context' => [
                'summary' => 'Update current user',
                'description' => 'Updated user information for current user',
            ],
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'profile' => [
            'method' => 'GET',
            'path' => '/profiles/celeb_{username}',
            'controller' => ProfileGetAction::class,
            'read' => false,
            'write' => false,
            'tag' => 'Profile',
            'output' => ProfileResponse::class,
            'openapi_context' => [
                'summary' => 'Get a profile',
                'description' => 'Get a profile of a user of the system. Auth is optional',
            ],
        ],
        'follow' => [
            'method' => 'POST',
            'path' => '/profiles/celeb_{username}/follow',
            'controller' => ProfileFollowAction::class,
            'read' => false,
            'write' => false,
            'tag' => 'Profile',
            'output' => ProfileResponse::class,
            'openapi_context' => [
                'summary' => 'Follow a user',
                'description' => 'Follow a user by username',
            ],
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
        ],
        'unfollow' => [
            'method' => 'DELETE',
            'status' => Response::HTTP_OK,
            'path' => '/profiles/celeb_{username}/follow',
            'controller' => ProfileUnfollowAction::class,
            'read' => false,
            'write' => false,
            'tag' => 'Profile',
            'output' => ProfileResponse::class,
            'openapi_context' => [
                'summary' => 'Unfollow a user',
                'description' => 'Unfollow a user by username',
            ],
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
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="followers")
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers')]
    public Collection $following;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    #[ORM\JoinTable(name: 'follower_user')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    public Collection $followers;

    public function __construct()
    {
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getPassword(): string
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

    public function follow(User $user): void
    {
        if ($user->followers->contains($this)) {
            return;
        }

        $user->followers->add($this);
    }

    public function unfollow(User $user): void
    {
        if (!$user->followers->contains($this)) {
            return;
        }

        $user->followers->removeElement($this);
    }
}
