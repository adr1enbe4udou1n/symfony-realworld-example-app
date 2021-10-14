<?php

namespace App\Feature\User\Action;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UpdateUserAction extends AbstractController
{
    public function __invoke(User $data)
    {
        return $data;
    }
}
