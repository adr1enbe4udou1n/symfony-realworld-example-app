<?php

namespace App\Feature\Profile\Response;

use App\Entity\User;
use App\Feature\Profile\DTO\ProfileDTO;

class ProfileResponse
{
    /**
     * @var ProfileDTO
     */
    public $profile;

    public static function make(User $user, ?User $currentUser)
    {
        $response = new self();
        $response->profile = $user->getProfile($currentUser);

        return $response;
    }
}
