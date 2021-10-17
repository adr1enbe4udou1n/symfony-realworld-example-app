<?php

namespace App\Tests\Article;

use App\Entity\Article;
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
        $this->createDefaultUser();

        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [
                'article' => $article,
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCannotCreateArticleWithSameTitle()
    {
        $this->em->persist((new Article())
            ->setTitle('Existing Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
        );
        $this->em->flush();

        $this->createDefaultUser();

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
        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanCreateArticle()
    {
        $this->createDefaultUser();

        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [
                'article' => [
                    'title' => 'Test Title',
                    'description' => 'Test Description',
                    'body' => 'Test Body',
                ],
            ],
        ]));

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains(['article' => [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'body' => 'Test Body',
            // 'author' => [
            //     'username' => 'John Doe',
            //     'bio' => 'John Bio',
            //     'image' => 'https://randomuser.me/api/portraits/women/1.jpg',
            // ],
        ]]);

        $this->assertNotNull(
            $this->orm->getRepository(Article::class)->findOneBy(['slug' => 'test-title'])
        );
    }
}
