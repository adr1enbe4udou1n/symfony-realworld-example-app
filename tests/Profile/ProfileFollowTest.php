<?php

namespace App\Tests\Profile;

use App\Entity\User;
use App\Tests\ApiBaseTestCase;

class ProfileFollowTest extends ApiBaseTestCase
{
    public function testGuestCannotFollowProfile()
    {
        $this->em->persist(
            (new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com')
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/profiles/John Doe/follow'));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanFollowProfile()
    {
        $this->actingAs();

        $this->em->persist(
            (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com')
        );

        $this->em->persist(
            (new User())
            ->setName('Alice Doe')
            ->setEmail('alice@example.com')
        );

        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/profiles/Jane Doe/follow'));

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

        $this->act(fn () => $this->client->request('DELETE', '/api/profiles/Jane Doe/follow'));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'profile' => [
                'username' => 'Jane Doe',
                'following' => false,
            ],
        ]);
    }
}
