<?php

namespace App\Controller\Profile;

use App\Dto\Profile\ProfileResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileUnfollowController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
    ) {
    }

    public function __invoke($username)
    {
        $user = $this->users->findOneBy(['name' => $username]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        /** @var User */
        $currentUser = $this->getUser();
        $currentUser->unfollow($user);
        $this->em->flush();

        return $this->json(new ProfileResponse($user, $currentUser));
    }
}
