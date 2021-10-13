<?php

namespace App\Feature\User\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use App\Feature\User\DTO\LoginUserDTO;
use App\Feature\User\Request\LoginUserRequest;

final class LoginUserDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param LoginUserRequest $data
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
