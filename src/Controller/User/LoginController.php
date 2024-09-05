<?php

namespace App\Controller\User;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\User\LoginUserRequest;
use App\Dto\User\UserResponse;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private UserRepository $users,
        private JWTTokenManagerInterface $jwtManager,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(LoginUserRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

        $user = $this->users->findOneBy(['email' => $data->user->email]);

        if (null === $user || !$this->userPasswordHasher->isPasswordValid($user, $data->user->password)) {
            return $this->json(['message' => 'Bad credentials'], 400);
        }

        return $this->json(new UserResponse($user, $this->jwtManager->create($user)));
    }
}
