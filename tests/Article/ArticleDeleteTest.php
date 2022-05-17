<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Tests\AbstractTest;

class ArticleDeleteTest extends AbstractTest
{
    public function testGuestCannotDeleteArticle()
    {
        $user = $this->createDefaultUser();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->act(fn () => $this->client->jsonRequest('DELETE', '/api/articles/test-title'));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCannotDeleteNonExistentArticle()
    {
        $this->actingAs();

        $this->act(fn () => $this->client->jsonRequest('DELETE', '/api/articles/test-title'));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCannotDeleteArticleOfOtherAuthor()
    {
        $user = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com');
        $this->em->persist($user);

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();

        $this->actingAs();

        $this->act(fn () => $this->client->jsonRequest('DELETE', '/api/articles/test-title'));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCanDeleteArticleWithAllComments()
    {
        $user = $this->createDefaultUser();
        $this->em->persist($user);

        $this->em->persist($article = (new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        $this->em->persist((new Comment())
            ->setBody('Test Body John')
            ->setAuthor($user)
            ->setArticle($article)
        );

        $this->em->persist($otherUser = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'));

        $this->em->persist((new Comment())
            ->setBody('Test Body Jane')
            ->setAuthor($otherUser)
            ->setArticle($article)
        );

        $this->em->flush();

        $this->actingAs($user);

        $this->act(fn () => $this->client->jsonRequest('DELETE', '/api/articles/test-title'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(
            0, $this->orm->getRepository(Article::class)->findAll()
        );

        $this->assertCount(
            0, $this->orm->getRepository(Comment::class)->findAll()
        );
    }
}
