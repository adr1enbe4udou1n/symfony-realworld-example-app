<?php

namespace App\Feature\User\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use App\Feature\User\Request\NewUserRequest;
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
     */
    public function transform($data, string $to, array $context = []): User
    {
        $this->validator->validate($data->user);

        $user = new User();
        $user->name = $data->user->username;
        $user->password = $this->userPasswordHasher->hashPassword($user, $data->user->password);
        $user->email = $data->user->email;

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

        return NewUserRequest::class === ($context['input']['class'] ?? null);
    }
}
