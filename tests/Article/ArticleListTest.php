<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Tests\AbstractTest;

class ArticleListTest extends AbstractTest
{
    private function createArticles()
    {
        $tag1 = (new Tag())->setName('Test Tag 1');
        $tag2 = (new Tag())->setName('Test Tag 2');
        $johnTag = (new Tag())->setName('Tag John Doe');
        $janeTag = (new Tag())->setName('Tag Jane Doe');

        $johnArticles = $this->createArticlesForAuthor((new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com'),
            30,
            [$tag1, $tag2, $johnTag]
        );
        $janeArticles = $this->createArticlesForAuthor((new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'),
            20,
            [$tag1, $tag2, $janeTag]
        );

        $jane = $janeArticles[0]->author;
        $jane->favorite($johnArticles[0]);
        $jane->favorite($johnArticles[1]);
        $jane->favorite($johnArticles[3]);
        $jane->favorite($johnArticles[7]);
        $jane->favorite($johnArticles[15]);
        $jane->follow($johnArticles[0]->author);
        $this->em->flush();

        $this->actingAs($jane);
    }

    /**
     * @return Article[]
     */
    private function createArticlesForAuthor(User $user, int $count, array $tags)
    {
        $articles = [];

        for ($i = 1; $i <= $count; ++$i) {
            $this->em->persist($articles[] = (new Article())
                ->setTitle("{$user->name} - Test Title {$i}")
                ->setDescription('Test Description')
                ->setBody('Test Body')
                ->setAuthor($user)
                ->addTag($tags[0])
                ->addTag($tags[1])
                ->addTag($tags[2])
            );
        }

        return $articles;
    }

    public function testCanPaginatedArticles()
    {
        $this->createArticles();

        $response = $this->act(fn () => $this->client->request('GET', '/api/articles?limit=20&offset=10'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(20, $response->toArray()['articles']);
        $this->assertEquals(50, $response->toArray()['articlesCount']);

        $this->assertJsonContains(['articles' => [
            0 => [
                'title' => 'Jane Doe - Test Title 10',
                'description' => 'Test Description',
                'body' => 'Test Body',
                'author' => [
                    'username' => 'Jane Doe',
                ],
                'tagList' => ['Tag Jane Doe', 'Test Tag 1', 'Test Tag 2'],
            ],
        ]]);
    }

    public function testCanFilterArticlesByAuthor()
    {
        $this->createArticles();

        $response = $this->act(fn () => $this->client->request('GET', '/api/articles?limit=10&offset=0&author=john'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(10, $response->toArray()['articles']);
        $this->assertEquals(30, $response->toArray()['articlesCount']);

        $this->assertJsonContains(['articles' => [
            0 => [
                'title' => 'John Doe - Test Title 30',
                'description' => 'Test Description',
                'body' => 'Test Body',
                'author' => [
                    'username' => 'John Doe',
                ],
                'tagList' => ['Tag John Doe', 'Test Tag 1', 'Test Tag 2'],
            ],
        ]]);
    }

    public function testCanFilterArticlesByTag()
    {
        $this->createArticles();

        $response = $this->act(fn () => $this->client->request('GET', '/api/articles?limit=10&offset=0&tag=jane'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(10, $response->toArray()['articles']);
        $this->assertEquals(20, $response->toArray()['articlesCount']);

        $this->assertJsonContains(['articles' => [
            0 => [
                'title' => 'Jane Doe - Test Title 20',
                'description' => 'Test Description',
                'body' => 'Test Body',
                'author' => [
                    'username' => 'Jane Doe',
                ],
                'tagList' => ['Tag Jane Doe'],
            ],
        ]]);
    }

    public function testCanFilterArticlesByFavorited()
    {
        $this->createArticles();

        $response = $this->act(fn () => $this->client->request('GET', '/api/articles?limit=10&offset=0&favorited=jane'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(5, $response->toArray()['articles']);
        $this->assertEquals(5, $response->toArray()['articlesCount']);

        $this->assertJsonContains(['articles' => [
            0 => [
                'title' => 'John Doe - Test Title 16',
                'description' => 'Test Description',
                'body' => 'Test Body',
                'author' => [
                    'username' => 'John Doe',
                ],
                'tagList' => ['Tag John Doe', 'Test Tag 1', 'Test Tag 2'],
                'favorited' => true,
                'favoritesCount' => 1,
            ],
        ]]);
    }

    public function testGuestCannotPaginateArticlesOfFollowedAuthors()
    {
        $this->act(fn () => $this->client->request('GET', '/api/articles/feed'));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanPaginateArticlesOfFollowedAuthors()
    {
        $this->createArticles();

        $response = $this->act(fn () => $this->client->request('GET', '/api/articles/feed?limit=10&offset=0'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(10, $response->toArray()['articles']);
        $this->assertEquals(30, $response->toArray()['articlesCount']);

        $this->assertJsonContains(['articles' => [
            0 => [
                'title' => 'John Doe - Test Title 30',
                'description' => 'Test Description',
                'body' => 'Test Body',
                'author' => [
                    'username' => 'John Doe',
                ],
                'tagList' => ['Tag John Doe', 'Test Tag 1', 'Test Tag 2'],
            ],
        ]]);
    }
}
