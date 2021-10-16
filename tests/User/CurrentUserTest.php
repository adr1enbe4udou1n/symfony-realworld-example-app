<?php

namespace App\Tests\User;

use App\Tests\AbstractTest;

class CurrentUserTest extends AbstractTest
{
    public function testUserCanFetchInfos(): void
    {
        $this->createDefaultUser();

        $this->client->request('GET', '/api/user');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['user' => [
            'email' => 'john.doe@example.com',
            'username' => 'John Doe',
        ]]);
    }

    public function testGuestUserCannotFetchInfos(): void
    {
        $this->client->request('GET', '/api/user');

        $this->assertResponseStatusCodeSame(401);
    }
}