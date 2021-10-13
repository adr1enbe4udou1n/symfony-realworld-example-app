<?php

namespace App\Feature\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDTO
{
    public ?string $username = null;

    #[Assert\Email]
    public ?string $email = null;

    public ?string $bio = null;

    public ?string $image = null;
}
