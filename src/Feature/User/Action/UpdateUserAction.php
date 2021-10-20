<?php

namespace App\Feature\User\Action;

use App\Entity\User;
use App\Feature\User\Response\UserResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateUserAction extends AbstractController
{
    public function __invoke(User $data, EntityManagerInterface $em, UserRepository $users, TokenStorageInterface $token)
    {
        /** @var User */
        $currentUser = $token->getToken()->getUser();

        if (($user = $users->findOneBy(['email' => $data->email])) && $user->id !== $currentUser->id) {
            return new JsonResponse('User with this email already exist', 400);
        }

        $em->persist($data);
        $em->flush();

        return new UserResponse($data);
    }
}
