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
    public function transform($data, string $to, array $context = []): UserResponse
    {
        $output = new UserResponse();
        $output->user = new UserDTO();
        $output->user->email = $data->email;
        $output->user->username = $data->name;
        $output->user->bio = $data->bio;
        $output->user->image = $data->image;
        $output->user->token = $this->jwtManager->create($data);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserResponse::class === $to && $data instanceof User;
    }
}