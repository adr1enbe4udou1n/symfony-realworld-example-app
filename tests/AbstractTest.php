<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected ?Client $client = null;

    public function setUp(): void
    {
        static::$dbPopulated = true;

        $this->client = static::createClient([], ['headers' => [
            'Accept' => 'application/json',
        ]]);
    }

    protected function createUser($user): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        $em->persist($user);
        $em->flush();

        $this->client->setDefaultOptions([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Token '.static::getContainer()
                    ->get('lexik_jwt_authentication.jwt_manager')
                    ->create($user),
            ],
        ]);

        return $user;
    }

    protected function createDefaultUser(?string $password = null): User
    {
        $user = new User();
        $user->name = 'John Doe';
        $user->email = 'john.doe@example.com';

        $user->bio = 'John Bio';
        $user->image = 'https://randomuser.me/api/portraits/men/1.jpg';

        if ($password) {
            $user->password = static::getContainer()
                ->get(UserPasswordHasherInterface::class)
                ->hashPassword($user, $password);
        }

        return $this->createUser($user);
    }
}
