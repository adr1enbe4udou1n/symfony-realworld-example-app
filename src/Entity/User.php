<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Feature\User\Action\CurrentUserAction;
use App\Feature\User\Action\LoginUserAction;
use App\Feature\User\Action\RegisterUserAction;
use App\Feature\User\Action\UpdateUserAction;
use App\Feature\User\Request\LoginUserRequest;
use App\Feature\User\Request\NewUserRequest;
use App\Feature\User\Request\UpdateUserRequest;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'public.user')]
#[UniqueEntity('email', message: 'user.email.unique')]
#[ApiResource(
    output: UserResponse::class,
    collectionOperations: [
        'post' => [
            'path' => '/users',
            'controller' => RegisterUserAction::class,
            'input' => NewUserRequest::class,
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
            'openapi_context' => [
                'summary' => 'Existing user login',
                'description' => 'Login for existing user',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/user',
            'controller' => CurrentUserAction::class,
            'read' => false,
            'openapi_context' => [
                'summary' => 'Get current user',
                'description' => 'Gets the currently logged-in user',
            ],
        ],
        'put' => [
            'path' => '/user',
            'controller' => UpdateUserAction::class,
            'input' => UpdateUserRequest::class,
            'read' => false,
            'openapi_context' => [
                'summary' => 'Update current user',
                'description' => 'Updated user information for current user',
            ],
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
    public ?string $password;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $bio;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $image;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    public \DateTime $updatedAt;

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
}
