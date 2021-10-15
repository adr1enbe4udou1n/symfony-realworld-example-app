<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Tests\AbstractTest;

class RegisterTest extends AbstractTest
{
    public function getInvalidRegisterData()
    {
        yield [[
            'email' => 'john.doe',
            'username' => 'John Doe',
            'password' => 'password',
        ]];

        yield [[
            'email' => 'john.doe@example.com',
        ]];

        yield [[
            'email' => 'john.doe@example.com',
            'username' => 'John Doe',
            'password' => 'pass',
        ]];
    }

    /**
     * @dataProvider getInvalidRegisterData
     */
    public function testUserCannotLoginWithInvalidData($data)
    {
        $this->client->request('POST', '/api/users', [
            'json' => [
                'user' => $data,
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUserCanRegister(): void
    {
        $response = $this->client->request('POST', '/api/users', [
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

    public function testUserCannotRegisterTwice(): void
    {
        $this->createDefaultUser();

        $this->client->request('POST', '/api/users', [
            'json' => [
                'user' => [
                    'email' => 'john.doe@example.com',
                    'password' => 'password',
                    'username' => 'John Doe',
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
