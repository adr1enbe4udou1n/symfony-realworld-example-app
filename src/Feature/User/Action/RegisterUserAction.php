<?php

namespace App\Feature\User\Action;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterUserAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
    ) {
    }

    public function __invoke(User $data)
    {
        $this->em->persist($data);

        return $data;
    }
}
