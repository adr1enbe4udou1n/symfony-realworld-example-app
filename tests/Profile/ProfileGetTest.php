<?php

namespace App\Tests\Profile;

use App\Entity\User;
use App\Tests\AbstractTest;

class ProfileGetTest extends AbstractTest
{
    public function testCanGetProfile()
    {
        $this->em->persist((new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com')
            ->setBio('My Bio')
            ->setImage('https://randomuser.me/api/portraits/men/1.jpg'));
        $this->em->flush();

        $this->act(fn () => $this->client->jsonRequest('GET', '/api/profiles/John Doe'));

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
        $this->act(fn () => $this->client->jsonRequest('GET', '/api/profiles/John Doe'));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCanGetFollowedProfile()
    {
        $followed = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com')
            ->setBio('Jane Bio')
            ->setImage('https://randomuser.me/api/portraits/women/1.jpg');

        $this->em->persist($followed);

        $user = (new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com')
            ->setBio('John Bio')
            ->setImage('https://randomuser.me/api/portraits/men/1.jpg');

        $user->follow($followed);

        $this->actingAs($user);

        $this->act(fn () => $this->client->jsonRequest('GET', '/api/profiles/Jane Doe'));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'profile' => [
                'username' => 'Jane Doe',
                'bio' => 'Jane Bio',
                'image' => 'https://randomuser.me/api/portraits/women/1.jpg',
                'following' => true,
            ],
        ]);
    }
}
