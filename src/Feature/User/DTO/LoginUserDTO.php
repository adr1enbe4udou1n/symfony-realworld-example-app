<?php

namespace App\Feature\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoginUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}
