<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Tests\ApiBaseTestCase;

class RegisterTest extends ApiBaseTestCase
{
    public static function getInvalidData()
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
     * @dataProvider getInvalidData
     */
    public function testUserCannotRegisterWithInvalidData($data)
    {
        $this->act(fn () => $this->client->request('POST', '/api/users', [
            'json' => [
                'user' => $data,
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUserCanRegister(): void
    {
        $this->act(fn () => $this->client->request('POST', '/api/users', [
            'json' => [
                'user' => [
                    'email' => 'john.doe@example.com',
                    'password' => 'password',
                    'username' => 'John Doe',
                ],
            ],
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['user' => [
            'email' => 'john.doe@example.com',
            'username' => 'John Doe',
        ]]);

        $this->assertNotNull(
            $this->orm->getRepository(User::class)->findOneBy(['email' => 'john.doe@example.com'])
        );
    }

    public function testUserCannotRegisterTwice(): void
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('POST', '/api/users', [
            'json' => [
                'user' => [
                    'email' => 'john.doe@example.com',
                    'password' => 'password',
                    'username' => 'John Doe',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(400);
    }
}
