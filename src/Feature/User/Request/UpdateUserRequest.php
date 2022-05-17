<?php

namespace App\Feature\User\Request;

use App\Feature\User\DTO\UpdateUserDTO;
use Symfony\Component\Validator\Constraints\Valid;

class UpdateUserRequest
{
    #[Valid]
    public UpdateUserDTO $user;
}
