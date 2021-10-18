<?php

namespace App\Feature\Profile\Action;

use App\Entity\User;
use App\Feature\Profile\Response\ProfileResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileFollowAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(User $data)
    {
        $this->getUser()->follow($data);
        $this->em->flush();

        return new ProfileResponse($data);
    }
}
