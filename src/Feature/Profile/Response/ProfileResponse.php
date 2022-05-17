<?php

namespace App\Feature\Profile\Response;

use App\Entity\User;
use App\Feature\Profile\DTO\ProfileDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileResponse
{
    /**
     * @var ProfileDTO
     */
    public $profile;

    public static function make(User $user, TokenStorageInterface $token)
    {
        $response = new self();
        $response->profile = $user->getProfile($token);

        return $response;
    }
}
