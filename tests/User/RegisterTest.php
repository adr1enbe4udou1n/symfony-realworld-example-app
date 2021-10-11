<?php

namespace App\Tests\User;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class RegisterTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/']);
    }
}
