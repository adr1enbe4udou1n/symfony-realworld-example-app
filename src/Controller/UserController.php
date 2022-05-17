<?php

namespace App\Controller;

use App\Entity\User;
use App\Feature\User\DTO\UpdateUserDTO;
use App\Feature\User\Request\NewUserRequest;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
        private JWTTokenManagerInterface $jwtManager,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    #[Route('/users', methods: ['POST'])]
    #[OA\Post(
        operationId: 'CreateUser',
        summary: 'Register a new user.',
        description: 'Register a new user',
        tags: ['User and Authentication'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: NewUserRequest::class)
            )
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: UserResponse::class)
                )
            ),
        ]
    )]
    public function register(NewUserRequest $data): Response
    {
        $this->validator->validate($data->user);

        $user = new User();
        $user->name = $data->user->username;
        $user->password = $this->userPasswordHasher->hashPassword($user, $data->user->password);
        $user->email = $data->user->email;

        if ($this->users->findOneBy(['email' => $data->user->email])) {
            return $this->json('User with this email already exist', 400);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(UserResponse::make($user, $this->jwtManager->create($user)));
    }

    #[Route('/users/login', methods: ['POST'])]
    #[OA\Post(
        operationId: 'Login',
        summary: 'Existing user login.',
        description: 'Login for existing user',
        tags: ['User and Authentication'],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: UserResponse::class)
                )
            ),
        ]
    )]
    public function login(): Response
    {
        return $this->json([]);
    }

    #[Route('/user', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetCurrentUser',
        summary: 'Get current user.',
        description: 'Gets the currently logged-in user',
        tags: ['User and Authentication'],
        security: [['Bearer' => []]],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: UserResponse::class)
                )
            ),
        ]
    )]
    public function current(): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->json(
            UserResponse::make($user, $this->jwtManager->create($user)),
        );
    }

    #[Route('/user', methods: ['PUT'])]
    #[OA\Put(
        operationId: 'UpdateCurrentUser',
        summary: 'Update current user.',
        description: 'Updated user information for current user',
        tags: ['User and Authentication'],
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: UpdateUserDTO::class)
            )
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: UserResponse::class)
                )
            ),
        ]
    )]
    public function update(UpdateUserDTO $data): Response
    {
        return $this->json([]);
    }
}
