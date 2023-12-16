<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints\Valid;

class LoginUserRequest
{
    #[Valid]
    public LoginUserDto $user;
}
