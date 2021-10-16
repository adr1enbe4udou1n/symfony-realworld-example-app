<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Closure;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected ?Client $client = null;
    protected ?Registry $orm = null;
    protected ?ObjectManager $em = null;

    public function setUp(): void
    {
        static::$dbPopulated = true;

        $this->client = static::createClient([], ['headers' => [
            'Accept' => 'application/json',
        ]]);

        $this->orm = static::getContainer()->get('doctrine');
        $this->em = $this->orm->getManager();
    }

    protected function createUser($user): User
    {
        $this->em->persist($user);
        $this->em->flush();

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

    public function act(Closure $act)
    {
        $this->orm->getManager()->clear();

        return $act();
    }
}
