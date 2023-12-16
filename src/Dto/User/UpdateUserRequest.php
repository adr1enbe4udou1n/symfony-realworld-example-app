<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints\Valid;

class UpdateUserRequest
{
    #[Valid]
    public UpdateUserDto $user;
}
