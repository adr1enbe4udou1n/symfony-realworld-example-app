<?php

namespace App\Feature\User\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\User;
use App\Feature\User\DTO\UserDTO;
use App\Feature\User\Response\UserResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class UserDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    /**
     * @param User $data
     */
    public function transform($data, string $to, array $context = []): UserDTO
    {
        $user = new UserDTO();
        $user->email = $data->email;
        $user->username = $data->name;
        $user->bio = $data->bio;
        $user->image = $data->image;
        $user->token = $this->jwtManager->create($data);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserResponse::class === $to && $data instanceof User;
    }
}
