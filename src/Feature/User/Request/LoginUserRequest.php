<?php

namespace App\Feature\User\Request;

use App\Feature\User\DTO\LoginUserDTO;
use Symfony\Component\Validator\Constraints\Valid;

class LoginUserRequest
{
    #[Valid]
    public LoginUserDTO $user;
}
