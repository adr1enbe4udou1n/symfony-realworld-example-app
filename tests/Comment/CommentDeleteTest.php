<?php

namespace App\Tests\Comment;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Tests\AbstractTest;

class CommentDeleteTest extends AbstractTest
{
    public function testGuestCannotDeleteComment()
    {
        $user = $this->createDefaultUser();

        $this->em->persist($comment = (new Comment())
            ->setBody('Test Body John')
            ->setAuthor($user)
            ->setArticle((new Article())
                ->setTitle('Test Title')
                ->setDescription('Test Description')
                ->setBody('Test Body')
                ->setAuthor($user)
            )
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('DELETE', "/api/articles/test-title/comments/{$comment->id}"));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCannotDeleteNonExistentComment()
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('DELETE', '/api/articles/test-title/comments/1'));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCannotDeleteCommentWithNonExistentArticle()
    {
        $user = $this->actingAs();

        $this->em->persist($article = (new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        $comment = (new Comment())
            ->setBody('Test Body John')
            ->setAuthor($user)
            ->setArticle($article);

        $this->em->persist($comment);

        $this->act(fn () => $this->client->request('DELETE', "/api/articles/other-title/comments/{$comment->id}"));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCanDeleteCommentWithBadArticle()
    {
        $user = $this->actingAs();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        $this->em->persist($article = (new Article())
            ->setTitle('Other Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        $comment = (new Comment())
            ->setBody('Test Body John')
            ->setAuthor($user)
            ->setArticle($article);

        $this->em->persist($comment);
        $this->em->flush();

        $this->act(fn () => $this->client->request('DELETE', "/api/articles/test-title/comments/{$comment->id}"));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCannotDeleteCommentOfOtherAuthor()
    {
        $this->em->persist($otherUser = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'));

        $this->em->persist($article = (new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($otherUser)
        );

        $this->em->persist($comment = (new Comment())
            ->setBody('Test Body Jane')
            ->setAuthor($otherUser)
            ->setArticle($article)
        );

        $this->actingAs();

        $this->act(fn () => $this->client->request('DELETE', "/api/articles/test-title/comments/{$comment->id}"));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCanDeleteOwnComment()
    {
        $user = $this->actingAs();

        $this->em->persist($article = (new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        $comment = (new Comment())
            ->setBody('Test Body John')
            ->setAuthor($user)
            ->setArticle($article);

        $this->em->persist($comment);
        $this->em->flush();

        $this->act(fn () => $this->client->request('DELETE', "/api/articles/test-title/comments/{$comment->id}"));

        $this->assertResponseIsSuccessful();

        $this->assertCount(
            0, $this->orm->getRepository(Comment::class)->findAll()
        );
    }

    public function testCanDeleteAllCommentsOfOwnArticle()
    {
        $user = $this->actingAs();

        $this->em->persist($article = (new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        $this->em->persist($otherUser = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com'));

        $comment = (new Comment())
            ->setBody('Test Body Jane')
            ->setAuthor($otherUser)
            ->setArticle($article);

        $this->em->persist($comment);
        $this->em->flush();

        $this->act(fn () => $this->client->request('DELETE', "/api/articles/test-title/comments/{$comment->id}"));

        $this->assertResponseIsSuccessful();

        $this->assertCount(
            0, $this->orm->getRepository(Comment::class)->findAll()
        );
    }
}
