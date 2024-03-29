<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDto
{
    #[Assert\NotBlank(allowNull: true)]
    public ?string $username = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    public ?string $email = null;

    public ?string $bio = null;

    public ?string $image = null;
}
