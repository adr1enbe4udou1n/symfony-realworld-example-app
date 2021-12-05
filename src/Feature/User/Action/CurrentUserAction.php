<?php

namespace App\Feature\User\Action;

use App\Entity\User;
use App\Feature\User\Response\UserResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrentUserAction extends AbstractController
{
    public function __invoke()
    {
        /** @var User */
        $user = $this->getUser();

        return new UserResponse($user);
    }
}
