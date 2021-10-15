<?php

namespace App\Tests\User;

use App\Tests\AbstractTest;

class LoginTest extends AbstractTest
{
    public function getInvalidCredentials()
    {
        yield [[
            'email' => 'jane.doe@example.com',
            'password' => 'password',
        ]];

        yield [[
            'email' => 'john.doe@example.com',
            'password' => 'badpassword',
        ]];
    }

    /**
     * @dataProvider getInvalidCredentials
     */
    public function testUserCannotLoginWithInvalidData($credentials)
    {
        $this->createDefaultUser('password');

        $this->client->request('POST', '/api/users/login', [
            'json' => [
                'user' => $credentials,
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testUserCanLogin(): void
    {
        $this->createDefaultUser('password');

        $this->client->request('POST', '/api/users/login', [
            'json' => [
                'user' => [
                    'email' => 'john.doe@example.com',
                    'password' => 'password',
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['user' => [
            'email' => 'john.doe@example.com',
            'username' => 'John Doe',
        ]]);
    }
}
