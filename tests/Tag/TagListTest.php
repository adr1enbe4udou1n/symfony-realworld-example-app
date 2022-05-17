<?php

namespace App\Tests\Tag;

use App\Entity\Tag;
use App\Tests\AbstractTest;

class TagListTest extends AbstractTest
{
    public function testCanListAllTags()
    {
        $this->em->persist((new Tag())->setName('Tag3'));
        $this->em->persist((new Tag())->setName('Tag2'));
        $this->em->persist((new Tag())->setName('Tag1'));
        $this->em->flush();

        $this->act(fn () => $this->client->jsonRequest('GET', '/api/tags'));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'tags' => ['Tag1', 'Tag2', 'Tag3'],
        ]);
    }
}
