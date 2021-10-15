<?php

namespace App\Feature\User\Action;

use App\Feature\User\DTO\LoginUserDTO;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginUserAction extends AbstractController
{
    public function __construct(
        private UserRepository $users,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function __invoke(LoginUserDTO $data)
    {
        $user = $this->users->findOneBy(['email' => $data->email]);

        if (null === $user || !$this->userPasswordHasher->isPasswordValid($user, $data->password)) {
            return new JsonResponse(['message' => 'Bad credentials'], 400);
        }

        return $user;
    }
}
