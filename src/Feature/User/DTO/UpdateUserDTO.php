<?php

namespace App\Feature\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDTO
{
    #[Assert\NotBlank(allowNull: true)]
    public ?string $username = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    public ?string $email = null;

    public ?string $bio = null;

    public ?string $image = null;
}
