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

    private ?string $token = null;

    public function setUp(): void
    {
        static::$dbPopulated = true;
    }

    protected function getClient(array $headers = []): Client
    {
        if ($this->token) {
            $headers += [
                'authorization' => 'Token '.$this->token,
            ];
        }

        return static::createClient([], ['headers' => [
            'Accept' => 'application/json',
        ] + $headers]);
    }

    protected function createUser($user = null): User
    {
        if (!$user) {
            $user = new User();
            $user->name = 'John Doe';
            $user->email = 'john.doe@example.com';
            $user->password = static::getContainer()
                ->get(UserPasswordHasherInterface::class)
                ->hashPassword($user, 'password');
            $user->bio = 'John Bio';
            $user->image = 'https://randomuser.me/api/portraits/men/1.jpg';
        }

        $em = static::getContainer()->get('doctrine')->getManager();

        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function actingAs($user = null): User
    {
        $user = $this->createUser($user);

        $this->token = static::getContainer()
            ->get('lexik_jwt_authentication.jwt_manager')
            ->create($user);

        return $user;
    }
}
