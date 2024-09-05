<?php

namespace App\Controller\User;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\User\NewUserRequest;
use App\Dto\User\UserResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
        private JWTTokenManagerInterface $jwtManager,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(NewUserRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

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

        return $this->json(new UserResponse($user, $this->jwtManager->create($user)));
    }
}
