<?php

namespace App\Feature\User\Response;

use App\Entity\User;
use App\Feature\User\DTO\UserDTO;

class UserResponse
{
    /**
     * @var UserDTO
     */
    public $user;

    public static function make(User $user, string $token)
    {
        $response = new self();

        $dto = new UserDTO();
        $dto->email = $user->email;
        $dto->username = $user->name;
        $dto->bio = $user->bio;
        $dto->image = $user->image;
        $dto->token = $token;
        $response->user = $dto;

        return $response;
    }
}
