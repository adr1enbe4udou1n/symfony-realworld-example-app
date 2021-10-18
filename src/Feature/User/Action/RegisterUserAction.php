<?php

namespace App\Feature\User\Action;

use App\Entity\User;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisterUserAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
    ) {
    }

    public function __invoke(User $data)
    {
        if ($this->users->findOneBy(['email' => $data->email])) {
            return new JsonResponse('User with this email already exist', 400);
        }

        $this->em->persist($data);
        $this->em->flush();

        return new UserResponse($data);
    }
}
