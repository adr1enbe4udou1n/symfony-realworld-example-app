<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Entity\Tag;
use App\Tests\AbstractTest;

class ArticleGetTest extends AbstractTest
{
    public function testCannotGetNonExistentArticle()
    {
        $this->act(fn () => $this->client->request('GET', '/api/articles/test-title'));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCanGetArticle()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
            ->addTag((new Tag())->setName('Test Tag 1'))
            ->addTag((new Tag())->setName('Test Tag 2'))
            ->addTag((new Tag())->setName('Tag John Doe'))
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('GET', '/api/articles/test-title'));

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains(['article' => [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'body' => 'Test Body',
            'author' => [
                'username' => 'John Doe',
                'bio' => 'John Bio',
                'image' => 'https://randomuser.me/api/portraits/men/1.jpg',
            ],
            'tagList' => ['Tag John Doe', 'Test Tag 1', 'Test Tag 2'],
        ]]);
    }
}
