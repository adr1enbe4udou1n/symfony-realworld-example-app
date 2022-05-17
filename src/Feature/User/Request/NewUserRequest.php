<?php

namespace App\Feature\User\Request;

use App\Feature\User\DTO\NewUserDTO;
use Symfony\Component\Validator\Constraints\Valid;

class NewUserRequest
{
    #[Valid]
    public NewUserDTO $user;
}
