<?php

namespace App\Tests\Profile;

use App\Entity\User;
use App\Tests\AbstractTest;

class ProfileGetTest extends AbstractTest
{
    public function testCanGetProfile()
    {
        $user = new User();
        $user->name = 'John Doe';
        $user->email = 'john.doe@example.com';
        $user->bio = 'My Bio';
        $user->image = 'https://randomuser.me/api/portraits/men/1.jpg';
        $this->em->persist($user);
        $this->em->flush();

        $this->client->request('GET', '/api/profiles/celeb_John Doe');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'profile' => [
                'username' => 'John Doe',
                'bio' => 'My Bio',
                'image' => 'https://randomuser.me/api/portraits/men/1.jpg',
                'following' => false,
            ],
        ]);
    }

    public function testCannotGetNonExistentProfile()
    {
        $this->client->request('GET', '/api/profiles/celeb_John Doe');

        $this->assertResponseStatusCodeSame(404);
    }
}
