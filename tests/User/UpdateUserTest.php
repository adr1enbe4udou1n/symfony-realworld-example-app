<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Tests\AbstractTest;

class UpdateUserTest extends AbstractTest
{
    public function getInvalidData()
    {
        yield [[
            'email' => 'john.doe',
            'username' => 'John Doe',
            'bio' => 'My Bio',
        ]];

        yield [[
            'email' => 'john.doe@example.com',
            'username' => '',
            'bio' => 'My Bio',
        ]];
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testCannotUpdateInfosWithInvalidData($data)
    {
        $this->createDefaultUser();

        $this->act(fn () => $this->client->request('PUT', '/api/user', [
            'json' => [
                'user' => $data,
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testLoggedUserCanUpdateInfos(): void
    {
        $this->createDefaultUser();

        $this->act(fn () => $this->client->request('PUT', '/api/user', [
            'json' => [
                'user' => [
                    'email' => 'jane.doe@example.com',
                    'bio' => 'My Bio',
                ],
            ],
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['user' => [
            'email' => 'jane.doe@example.com',
            'username' => 'John Doe',
            'bio' => 'My Bio',
        ]]);

        $this->assertNotNull(
            $this->orm->getRepository(User::class)->findOneBy(['email' => 'jane.doe@example.com'])
        );
    }

    public function testGuestUserCannotUpdateInfos(): void
    {
        $this->act(fn () => $this->client->request('PUT', '/api/user'));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoggedUserCannotUpdateWithAlreadyUsedEmail(): void
    {
        $user = new User();
        $user->name = 'Jane Doe';
        $user->email = 'jane.doe@example.com';
        $this->em->persist($user);
        $this->em->flush();

        $this->createDefaultUser();

        $this->act(fn () => $this->client->request('PUT', '/api/user', [
            'json' => [
                'user' => [
                    'email' => 'jane.doe@example.com',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
