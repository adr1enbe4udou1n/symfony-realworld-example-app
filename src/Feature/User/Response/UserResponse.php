<?php

namespace App\Feature\User\Response;

use App\Entity\User;
use App\Feature\User\DTO\UserDTO;

class UserResponse
{
    /**
     * @var UserDTO|User
     */
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
