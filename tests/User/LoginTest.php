<?php

namespace App\Tests\User;

use App\Tests\AbstractTest;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends AbstractTest
{
    public function getInvalidData()
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
     * @dataProvider getInvalidData
     */
    public function testUserCannotLoginWithInvalidData($credentials)
    {
        $user = $this->createDefaultUser();

        $user->password = static::getContainer()
            ->get(UserPasswordHasherInterface::class)
            ->hashPassword($user, 'password');

        $this->em->persist($user);
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/users/login', [
            'json' => [
                'user' => $credentials,
            ],
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testUserCanLogin(): void
    {
        $user = $this->createDefaultUser();

        $user->password = static::getContainer()
            ->get(UserPasswordHasherInterface::class)
            ->hashPassword($user, 'password');

        $this->em->persist($user);
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/users/login', [
            'json' => [
                'user' => [
                    'email' => 'john.doe@example.com',
                    'password' => 'password',
                ],
            ],
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['user' => [
            'email' => 'john.doe@example.com',
            'username' => 'John Doe',
        ]]);
    }
}
