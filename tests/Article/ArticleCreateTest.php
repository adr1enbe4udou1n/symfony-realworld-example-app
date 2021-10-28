<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Entity\Tag;
use App\Tests\AbstractTest;

class ArticleCreateTest extends AbstractTest
{
    public function getInvalidData()
    {
        yield [[
            'title' => '',
            'description' => 'Test Description',
            'body' => 'Test Body',
        ]];

        yield [[
            'title' => 'Test Title',
            'description' => '',
            'body' => 'Test Body',
        ]];

        yield [[
            'title' => 'Test Title',
            'description' => 'Test Description',
            'body' => '',
        ]];
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testCannotCreateArticleWithInvalidData($article)
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [
                'article' => $article,
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCannotCreateArticleWithSameTitle()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Existing Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [
                'article' => [
                    'title' => 'Existing Title',
                    'description' => 'Test Description',
                    'body' => 'Test Body',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testGuestCannotCreateArticle()
    {
        $this->act(fn () => $this->client->request('POST', '/api/articles'));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanCreateArticle()
    {
        $this->em->persist((new Tag())->setName('Existing Tag'));
        $this->em->flush();

        $this->actingAs();

        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [
                'article' => [
                    'title' => 'Test Title',
                    'description' => 'Test Description',
                    'body' => 'Test Body',
                    'tagList' => ['Test Tag 1', 'Test Tag 2', 'Existing Tag'],
                ],
            ],
        ]));

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
            'tagList' => ['Existing Tag', 'Test Tag 1', 'Test Tag 2'],
        ]]);

        $this->assertNotNull(
            $this->orm->getRepository(Article::class)->findOneBy(['slug' => 'test-title'])
        );

        $this->assertCount(
            3, $this->orm->getRepository(Tag::class)->findAll()
        );
    }
}
