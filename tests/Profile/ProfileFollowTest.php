<?php

namespace App\Tests\Profile;

use App\Entity\User;
use App\Tests\AbstractTest;

class ProfileFollowTest extends AbstractTest
{
    public function testGuestCannotFollowProfile()
    {
        $toFollow = new User();
        $toFollow->name = 'John Doe';
        $toFollow->email = 'john.doe@example.com';

        $this->em->persist($toFollow);
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/profiles/celeb_John Doe/follow', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanFollowProfile()
    {
        $toFollow = new User();
        $toFollow->name = 'Jane Doe';
        $toFollow->email = 'jane.doe@example.com';

        $other = new User();
        $other->name = 'Alice Doe';
        $other->email = 'alice@example.com';

        $this->em->persist($toFollow);
        $this->em->persist($other);

        $this->createDefaultUser();

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
        $toUnfollow = new User();
        $toUnfollow->name = 'Jane Doe';
        $toUnfollow->email = 'jane.doe@example.com';

        $other = new User();
        $other->name = 'Alice Doe';
        $other->email = 'alice@example.com';

        $this->em->persist($toUnfollow);
        $this->em->persist($other);

        $user = new User();
        $user->name = 'John Doe';
        $user->email = 'john.doe@example.com';

        $user->following->add($toUnfollow);
        $user->following->add($other);

        $this->createUser($user);

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
