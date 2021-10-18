<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Tests\AbstractTest;

class ArticleFavoriteTest extends AbstractTest
{
    public function testGuestCannotFavoriteArticle()
    {
        $user = $this->createDefaultUser();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/favorite', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCannotFavoriteNonExistentArticle()
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/favorite', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCanFavoriteArticle()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/favorite', [
            'json' => [],
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
            'favorited' => true,
            'favoritesCount' => 1,
        ]]);

        $this->assertCount(
            1, $this->orm->getRepository(Article::class)->findOneBy(['slug' => 'test-title'])->favoritedBy
        );
    }

    public function testCanUnfavoriteArticle()
    {
        $user = $this->actingAs();

        $user->favorite((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('DELETE', '/api/articles/test-title/favorite'));

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
            'favorited' => false,
            'favoritesCount' => 0,
        ]]);

        $this->assertCount(
            0, $this->orm->getRepository(Article::class)->findOneBy(['slug' => 'test-title'])->favoritedBy
        );
    }
}
