<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\UserDTO;
use App\DTO\UserResponse;
use App\Entity\User;

final class UserDataTransformer implements DataTransformerInterface
{
    /**
     * @param User $data
     */
    public function transform($data, string $to, array $context = []): UserResponse
    {
        $output = new UserResponse();
        $output->user = new UserDTO();
        $output->user->email = $data->email;
        $output->user->username = $data->username;
        $output->user->bio = $data->bio;
        $output->user->image = $data->image;
        $output->user->token = 'token';

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserDTO::class === $to && $data instanceof User;
    }
}
