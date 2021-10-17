<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response;
use App\Entity\User;
use Closure;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

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

    protected function createDefaultUser()
    {
        return (new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com')
            ->setBio('John Bio')
            ->setImage('https://randomuser.me/api/portraits/men/1.jpg');
    }

    protected function actingAs($user = null): User
    {
        if (!$user) {
            $user = $this->createDefaultUser();
        }

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

    public function act(Closure $act): Response
    {
        $this->orm->getManager()->clear();

        return $act();
    }
}
