<?php

namespace App\Controller\Profile;

use App\Dto\Profile\ProfileResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileFollowController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
    ) {
    }

    public function __invoke(#[MapEntity(mapping: ['username' => 'name'])] User $user)
    {
        /** @var User */
        $currentUser = $this->getUser();
        $currentUser->follow($user);
        $this->em->flush();

        return $this->json(new ProfileResponse($user, $currentUser));
    }
}
