<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DTO\UpdateUserRequest;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UpdateUserDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private TokenStorageInterface $token,
    ) {
    }

    /**
     * @param UpdateUserRequest $data
     *                                {@inheritdoc}
     */
    public function transform($data, string $to, array $context = []): User
    {
        $this->validator->validate($data->user);

        $user = $this->token->getToken()->getUser();
        $user->name = $data->user->username ?? $user->name;
        $user->email = $data->user->email ?? $user->email;
        $user->bio = $data->user->bio ?? $user->bio;
        $user->image = $data->user->image ?? $user->image;

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

        return UpdateUserRequest::class === ($context['input']['class'] ?? null);
    }
}
