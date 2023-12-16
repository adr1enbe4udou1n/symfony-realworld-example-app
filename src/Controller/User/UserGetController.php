<?php

namespace App\Controller\User;

use App\Dto\User\UserResponse;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserGetController extends AbstractController
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function __invoke()
    {
        /** @var User */
        $user = $this->getUser();

        return $this->json(new UserResponse($user, $this->jwtManager->create($user)));
    }
}
