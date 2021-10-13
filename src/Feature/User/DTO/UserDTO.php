<?php

namespace App\Feature\User\DTO;

class UserDTO
{
    public string $email;

    public string $username;

    public ?string $bio;

    public ?string $image;

    public ?string $token;
}
