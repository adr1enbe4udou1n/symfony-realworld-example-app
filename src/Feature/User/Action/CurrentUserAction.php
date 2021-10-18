<?php

namespace App\Feature\User\Action;

use App\Feature\User\Response\UserResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrentUserAction extends AbstractController
{
    public function __invoke()
    {
        return new UserResponse($this->getUser());
    }
}
