<?php

namespace App\Tests\Profile;

use App\Entity\User;
use App\Tests\AbstractTest;

class ProfileFollowTest extends AbstractTest
{
    public function testGuestCannotFollowProfile()
    {
        $this->act(fn () => $this->client->request('POST', '/api/profiles/celeb_John Doe/follow', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanFollowProfile()
    {
        $this->actingAs();

        $this->em->persist((new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com')
        );

        $this->em->persist((new User())
            ->setName('Alice Doe')
            ->setEmail('alice@example.com')
        );

        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/profiles/celeb_Jane Doe/follow', [
            'json' => [],
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'profile' => [
                'username' => 'Jane Doe',
                'following' => true,
            ],
        ]);
    }

    public function testCanUnfollowProfile()
    {
        $user = $this->actingAs();

        $user->following->add((new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'));
        $user->following->add((new User())
            ->setName('Alice')
            ->setEmail('alice@example.com'));

        $this->em->flush();

        $this->act(fn () => $this->client->request('DELETE', '/api/profiles/celeb_Jane Doe/follow'));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'profile' => [
                'username' => 'Jane Doe',
                'following' => false,
            ],
        ]);
    }
}
