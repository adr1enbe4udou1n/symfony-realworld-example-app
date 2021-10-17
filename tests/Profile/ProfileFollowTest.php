<?php

namespace App\Tests\Profile;

use App\Entity\User;
use App\Tests\AbstractTest;

class ProfileFollowTest extends AbstractTest
{
    public function testGuestCannotFollowProfile()
    {
        $this->em->persist((new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com')
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/profiles/celeb_John Doe/follow', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanFollowProfile()
    {
        $this->em->persist((new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com')
        );

        $this->em->persist((new User())
            ->setName('Alice Doe')
            ->setEmail('alice@example.com')
        );

        $this->actingAs();

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
        $this->em->persist($toUnfollow = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'));
        $this->em->persist($other = (new User())
            ->setName('Alice')
            ->setEmail('alice@example.com'));

        $user = (new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com');

        $user->following->add($toUnfollow);
        $user->following->add($other);

        $this->actingAs($user);

        $this->act(fn () => $this->client->request('DELETE', '/api/profiles/celeb_Jane Doe/follow', [
            'json' => [],
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'profile' => [
                'username' => 'Jane Doe',
                'following' => false,
            ],
        ]);
    }
}
