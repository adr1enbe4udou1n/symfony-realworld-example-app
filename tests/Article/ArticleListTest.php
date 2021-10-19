<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Entity\User;
use App\Tests\AbstractTest;

class ArticleListTest extends AbstractTest
{
    private function createArticles()
    {
        $this->createArticlesForAuthor((new User())
            ->setName('John Doe')
            ->setEmail('john.doe@example.com'),
            30
        );
        $this->createArticlesForAuthor((new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'),
            20
        );
    }

    private function createArticlesForAuthor(User $user, int $count)
    {
        for ($i = 1; $i <= $count; ++$i) {
            $this->em->persist((new Article())
                ->setTitle("{$user->name} - Test Title {$i}")
                ->setDescription('Test Description')
                ->setBody('Test Body')
                ->setAuthor($user)
            );
        }
        $this->em->flush();
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
            ],
        ]]);
    }

    public function testCanFilterArticlesByAuthor()
    {
    }

    public function testCanFilterArticlesByTag()
    {
    }

    public function testCanFilterArticlesByFavorited()
    {
    }

    public function testCanPaginateArticlesOfFollowedAuthors()
    {
    }
}
