<?php

namespace App\Feature\Profile\Action;

use App\Entity\User;
use App\Feature\Profile\Response\ProfileResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileUnfollowAction extends AbstractController
{
    public function __invoke(User $data, EntityManagerInterface $em)
    {
        /** @var User */
        $user = $this->getUser();
        $user->unfollow($data);
        $em->flush();

        return new ProfileResponse($data);
    }
}
