<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseState;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ApiBaseTestCase extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected ?Client $client = null;
    protected ?Registry $orm = null;
    protected ?ObjectManager $em = null;

    public function setUp(): void
    {
        RefreshDatabaseState::setDbPopulated(true);

        $this->client = static::createClient();

        $this->client->setDefaultOptions([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

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
                'Content-Type' => 'application/json',
                'Authorization' => 'Token '.static::getContainer()
                    ->get('lexik_jwt_authentication.jwt_manager')
                    ->create($user),
            ],
        ]);

        return $user;
    }

    protected function act(\Closure $act)
    {
        $this->orm->getManager()->clear();

        $act();

        if (204 != $this->client->getResponse()->getStatusCode()) {
            return $this->client->getResponse()->toArray(false);
        }
    }
}
