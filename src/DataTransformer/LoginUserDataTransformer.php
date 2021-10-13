<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DTO\LoginUserDTO;
use App\DTO\LoginUserRequest;
use App\Entity\User;

final class LoginUserDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param LoginUserRequest $data
     *                               {@inheritdoc}
     */
    public function transform($data, string $to, array $context = []): LoginUserDTO
    {
        $this->validator->validate($data->user);

        return $data->user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return LoginUserRequest::class === ($context['input']['class'] ?? null);
    }
}
