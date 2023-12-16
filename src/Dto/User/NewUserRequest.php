<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints\Valid;

class NewUserRequest
{
    #[Valid]
    public NewUserDto $user;
}
