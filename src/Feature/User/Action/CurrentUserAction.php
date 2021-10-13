<?php

namespace App\Feature\User\Action;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrentUserAction extends AbstractController
{
    public function __invoke()
    {
        return $this->getUser();
    }
}
