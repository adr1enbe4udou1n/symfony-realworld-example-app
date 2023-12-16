<?php

namespace App\Dto\User;

use App\Entity\User;

class UserResponse
{
    /**
     * @var UserDto
     */
    public $user;

    public function __construct(User $user, string $token)
    {
        $dto = new UserDto();
        $dto->email = $user->email;
        $dto->username = $user->name;
        $dto->bio = $user->bio;
        $dto->image = $user->image;
        $dto->token = $token;
        $this->user = $dto;
    }
}
