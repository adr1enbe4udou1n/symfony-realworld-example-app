<?php

namespace App\Test\Comment;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Tests\ApiBaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CommentCreateTest extends ApiBaseTestCase
{
    private function createArticle()
    {
        $user = (new User())
            ->setName('Jane Doe')
            ->setEmail('jane.doe@example.com')
            ->setBio('Jane Bio')
            ->setImage('https://randomuser.me/api/portraits/women/1.jpg');
        $this->em->persist($user);

        $this->em->persist(
            (new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );
        $this->em->flush();
    }

    public static function getInvalidData()
    {
        yield [[
            'body' => '',
        ]];
    }

    #[DataProvider('getInvalidData')]
    public function testCannotCreateCommentWithInvalidData($comment)
    {
        $this->createArticle();
        $this->actingAs();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/comments', [
            'json' => [
                'comment' => $comment,
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCannotCreateCommentToNonExistentArticle()
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/comments', [
            'json' => [
                'comment' => [
                    'body' => 'New Comment',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGuestCannotCreateComment()
    {
        $this->createArticle();

        $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/comments', [
            'json' => [
                'comment' => [
                    'body' => 'New Comment',
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCanCreateComment()
    {
        $this->createArticle();
        $this->actingAs();

        $response = $this->act(fn () => $this->client->request('POST', '/api/articles/test-title/comments', [
            'json' => [
                'comment' => [
                    'body' => 'New Comment',
                ],
            ],
        ]));

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains(['comment' => [
            'body' => 'New Comment',
            'author' => [
                'username' => 'John Doe',
                'bio' => 'John Bio',
                'image' => 'https://randomuser.me/api/portraits/men/1.jpg',
            ],
        ]]);

        $this->assertNotNull(
            $this->em->getRepository(Comment::class)->find($response['comment']['id'])
        );
    }
}
