<?php

namespace App\Controller;

use App\Entity\User;
use App\Feature\User\Request\LoginUserRequest;
use App\Feature\User\Request\NewUserRequest;
use App\Feature\User\Request\UpdateUserRequest;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
        private JWTTokenManagerInterface $jwtManager,
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
    #[ParamConverter('data', converter: 'fos_rest.request_body')]
    public function register(NewUserRequest $data, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, 422);
        }

        $user = new User();
        $user->name = $data->user->username;
        $user->password = $this->userPasswordHasher->hashPassword($user, $data->user->password);
        $user->email = $data->user->email;

        if ($this->users->findOneBy(['email' => $data->user->email])) {
            return $this->json([
                'message' => 'User with this email already exist',
            ], 400);
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
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: LoginUserRequest::class)
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
    #[ParamConverter('data', converter: 'fos_rest.request_body')]
    public function login(LoginUserRequest $data, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, 422);
        }

        $user = $this->users->findOneBy(['email' => $data->user->email]);

        if (null === $user || !$this->userPasswordHasher->isPasswordValid($user, $data->user->password)) {
            return $this->json(['message' => 'Bad credentials'], 400);
        }

        return $this->json(UserResponse::make($user, $this->jwtManager->create($user)));
    }

    #[Route('/user', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
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
    #[IsGranted('ROLE_USER')]
    #[OA\Put(
        operationId: 'UpdateCurrentUser',
        summary: 'Update current user.',
        description: 'Updated user information for current user',
        tags: ['User and Authentication'],
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: UpdateUserRequest::class)
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
    #[ParamConverter('data', converter: 'fos_rest.request_body')]
    public function update(UpdateUserRequest $data, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, 422);
        }

        /** @var User */
        $user = $this->getUser();

        if (($existingUser = $this->users->findOneBy(['email' => $data->user->email])) && $existingUser->id !== $user->id) {
            return $this->json(['message' => 'User with this email already exist'], 400);
        }

        $user->name = $data->user->username ?? $user->name;
        $user->email = $data->user->email ?? $user->email;
        $user->bio = $data->user->bio ?? $user->bio;
        $user->image = $data->user->image ?? $user->image;

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(UserResponse::make($user, $this->jwtManager->create($user)));
    }
}
