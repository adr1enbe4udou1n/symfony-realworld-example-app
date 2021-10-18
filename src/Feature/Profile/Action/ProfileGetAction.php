<?php

namespace App\Feature\Profile\Action;

use App\Entity\User;
use App\Feature\Profile\Response\ProfileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileGetAction extends AbstractController
{
    public function __invoke(User $data)
    {
        return new ProfileResponse($data);
    }
}
