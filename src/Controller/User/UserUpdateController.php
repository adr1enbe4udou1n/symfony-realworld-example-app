<?php

namespace App\Controller\User;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\User\UpdateUserRequest;
use App\Dto\User\UserResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserUpdateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
        private JWTTokenManagerInterface $jwtManager
    ) {
    }

    public function __invoke(UpdateUserRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

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

        return $this->json(new UserResponse($user, $this->jwtManager->create($user)));
    }
}
