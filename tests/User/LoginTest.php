<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Tests\AbstractTest;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        $client = $this->getClient();

        $user = new User();
        $user->name = 'John Doe';
        $user->email = 'john.doe@example.com';
        $user->password = static::getContainer()
            ->get(UserPasswordHasherInterface::class)
            ->hashPassword($user, 'password');

        $em = static::getContainer()->get('doctrine')->getManager();

        $em->persist($user);
        $em->flush();

        $client->request('POST', '/api/users/login', [
            'json' => [
                'user' => $credentials,
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testUserCanLogin(): void
    {
        $client = $this->getClient();

        $user = new User();
        $user->name = 'John Doe';
        $user->email = 'john.doe@example.com';
        $user->password = static::getContainer()
            ->get(UserPasswordHasherInterface::class)
            ->hashPassword($user, 'password');

        $em = static::getContainer()->get('doctrine')->getManager();

        $em->persist($user);
        $em->flush();

        $client->request('POST', '/api/users/login', [
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
