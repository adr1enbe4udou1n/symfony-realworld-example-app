<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UpdateUserAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(User $data)
    {
        $this->em->persist($data);

        return $data;
    }
}
