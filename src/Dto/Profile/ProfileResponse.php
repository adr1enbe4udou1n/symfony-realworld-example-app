<?php

namespace App\Dto\Profile;

use App\Entity\User;

class ProfileResponse
{
    /**
     * @var ProfileDto
     */
    public $profile;

    public function __construct(User $user, ?User $currentUser)
    {
        $this->profile = $user->getProfile($currentUser);
    }
}
