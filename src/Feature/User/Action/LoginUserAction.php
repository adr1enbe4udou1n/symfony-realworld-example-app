<?php

namespace App\Feature\User\Action;

use App\Feature\User\DTO\LoginUserDTO;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginUserAction extends AbstractController
{
    public function __invoke(LoginUserDTO $data, UserRepository $users, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = $users->findOneBy(['email' => $data->email]);

        if (null === $user || !$userPasswordHasher->isPasswordValid($user, $data->password)) {
            return new JsonResponse(['message' => 'Bad credentials'], 400);
        }

        return new UserResponse($user);
    }
}
