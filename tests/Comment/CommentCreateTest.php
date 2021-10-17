<?php

namespace App\Test\Comment;

use App\Entity\Article;
use App\Tests\AbstractTest;

class CommentCreateTest extends AbstractTest
{
    private function createArticle()
    {
        $user = $this->createDefaultUser();

        $this->em->persist((new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();
    }

    public function getInvalidData()
    {
        yield [[
            'body' => '',
        ]];
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testCannotCreateCommentWithInvalidData($comment)
    {
    }

    public function testCannotCreateCommentToNonExistentArticle()
    {
        $this->createDefaultUser();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/comments', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGuestCannotCreateComment()
    {
        $this->createArticle();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/comments', [
            'json' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanCreateComment()
    {
    }
}
