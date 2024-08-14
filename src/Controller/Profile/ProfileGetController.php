<?php

namespace App\Controller\Profile;

use App\Dto\Profile\ProfileResponse;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileGetController extends AbstractController
{
    public function __invoke(#[MapEntity(mapping: ['username' => 'name'])] User $user)
    {
        /** @var User */
        $currentUser = $this->getUser();

        return $this->json(new ProfileResponse($user, $currentUser));
    }
}
