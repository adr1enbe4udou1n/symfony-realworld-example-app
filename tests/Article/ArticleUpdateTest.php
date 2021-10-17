<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Entity\User;
use App\Tests\AbstractTest;

class ArticleUpdateTest extends AbstractTest
{
    public function testGuestCannotUpdateArticle()
    {
        $this->act(fn () => $this->client->request('PUT', '/api/articles/test-title', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGuestCannotUpdateNonExistentArticle()
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('PUT', '/api/articles/test-title', [
            'json' => [
                'article' => [
                    'title' => 'Test Title',
                    'description' => 'Test Description',
                    'body' => 'Test Body',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function getInvalidData()
    {
        yield [[
            'title' => 'Test Title',
            'description' => 'Test Description',
            'body' => '',
        ]];
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testCannotUpdateArticleWithInvalidData($article)
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
                ->setTitle('Test Title')
                ->setDescription('Test Description')
                ->setBody('Test Body')
                ->setAuthor($user)
            );
        $this->em->flush();

        $this->act(fn () => $this->client->request('PUT', '/api/articles/test-title', [
            'json' => [
                'article' => $article,
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCannotUpdateArticleWithSameTitle()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Existing Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('PUT', '/api/articles/test-title', [
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

    public function testCannotUpdateArticleOfOtherAuthor()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $user = $this->actingAs((new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com')
        );

        $this->act(fn () => $this->client->request('PUT', '/api/articles/test-title', [
            'json' => [
                'article' => [
                    'title' => 'New Title',
                    'description' => 'Test Description',
                    'body' => 'Test Body',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCanUpdateOwnArticle()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('PUT', '/api/articles/test-title', [
            'json' => [
                'article' => [
                    'title' => 'New Title',
                ],
            ],
        ]));

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains(['article' => [
            'title' => 'New Title',
            'description' => 'Test Description',
            'body' => 'Test Body',
            'author' => [
                'username' => 'John Doe',
                'bio' => 'John Bio',
                'image' => 'https://randomuser.me/api/portraits/men/1.jpg',
            ],
        ]]);

        $this->assertNotNull(
            $this->orm->getRepository(Article::class)->findOneBy(['title' => 'New Title'])
        );
    }
}
