<?php

namespace App\Feature\Profile\Response;

use App\Entity\User;
use App\Feature\Profile\DTO\ProfileDTO;

class ProfileResponse
{
    /**
     * @var ProfileDTO|User
     */
    public $profile;

    public function __construct(User $profile)
    {
        $this->profile = $profile;
    }
}
