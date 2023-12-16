<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class LoginUserDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}
