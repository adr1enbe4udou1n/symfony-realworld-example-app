<?php

// src/DataTransformer/BookInputDataTransformer.php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DTO\NewUserRequest;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class NewUserDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @param NewUserRequest $data
     *                             {@inheritdoc}
     */
    public function transform($data, string $to, array $context = []): User
    {
        $this->validator->validate($data->user);

        $user = new User();
        $user->name = $data->user->username;
        $user->password = $this->userPasswordHasher->hashPassword($user, $data->user->password);
        $user->email = $data->user->username;

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
