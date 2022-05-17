<?php

namespace App\Tests;

use App\Entity\User;
use Closure;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\Persistence\ObjectManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use ArraySubsetAsserts;

    protected ?KernelBrowser $client = null;
    protected ?Registry $orm = null;
    protected ?ObjectManager $em = null;
    protected ?DebugStack $debugStack = null;
    protected ?array $response = null;

    public function setUp(): void
    {
        static::$dbPopulated = true;

        $this->client = static::createClient();

        $this->orm = static::getContainer()->get('doctrine');
        $this->em = $this->orm->getManager();

        $this->debugStack = new DebugStack();
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

        $this->client->setServerParameters([
            'HTTP_AUTHORIZATION' => 'Token '.static::getContainer()
                ->get('lexik_jwt_authentication.jwt_manager')
                ->create($user),
        ]);

        return $user;
    }

    public function act(Closure $act): ?array
    {
        $this->orm->getManager()->clear();

        $this->orm->getConnection()
            ->getConfiguration()
            ->setSQLLogger($this->debugStack);

        $act();

        echo sprintf("Number of SQL queries : %d\n\n", count($this->debugStack->queries));

        return $this->response = json_decode($this->client->getResponse()->getContent(), true);
    }

    public function assertJsonContains(array $data)
    {
        $this->assertArraySubset($data, $this->response, true);
    }
}
