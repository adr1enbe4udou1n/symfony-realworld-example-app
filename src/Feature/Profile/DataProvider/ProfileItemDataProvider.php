<?php

namespace App\Feature\Profile\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;

final class ProfileItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private UserRepository $users,
    ) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $username, string $operationName = null, array $context = []): ?User
    {
        return $this->users->findOneBy(['name' => $username]);
    }
}
