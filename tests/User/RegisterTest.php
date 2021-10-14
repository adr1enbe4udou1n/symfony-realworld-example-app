<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Tests\AbstractTest;

class RegisterTest extends AbstractTest
{
    public function testUserCanRegister(): void
    {
        $this->getClient()->request('POST', '/api/users', [
            'json' => [
                'user' => [
                    'email' => 'john.doe@example.com',
                    'password' => 'password',
                    'username' => 'John Doe',
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['user' => [
            'email' => 'john.doe@example.com',
            'username' => 'John Doe',
        ]]);

        $this->assertNotNull(
            static::getContainer()->get('doctrine')
                ->getRepository(User::class)->findOneBy(['email' => 'john.doe@example.com'])
        );
    }
}
