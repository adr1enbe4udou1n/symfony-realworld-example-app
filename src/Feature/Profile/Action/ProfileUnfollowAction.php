<?php

namespace App\Feature\Profile\Action;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileUnfollowAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(User $data)
    {
        $this->getUser()->unfollow($data);
        $this->em->flush();

        return $data;
    }
}
