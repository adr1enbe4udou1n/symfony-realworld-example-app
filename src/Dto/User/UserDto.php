<?php

namespace App\Dto\User;

class UserDto
{
    public string $email;

    public string $username;

    public ?string $bio;

    public ?string $image;

    public ?string $token;
}
