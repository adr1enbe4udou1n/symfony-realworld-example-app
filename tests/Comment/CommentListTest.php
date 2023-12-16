<?php

namespace App\Tests\Comment;

use App\Entity\Article;
use App\Entity\Comment;
use App\Tests\ApiBaseTestCase;

class CommentListTest extends ApiBaseTestCase
{
    public function testCannotListCommentsOfNonExistentArticle()
    {
        $this->act(fn () => $this->client->request('GET', '/api/articles/test-title/comments'));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCanListAllCommentsOfArticle()
    {
        $user = $this->createDefaultUser();

        $this->em->persist(
            ($article = new Article())
            ->setTitle('Test Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
            ->setAuthor($user)
        );

        for ($i = 1; $i <= 20; ++$i) {
            $article->addComment((new Comment())
                ->setBody("Test Comment $i")
                ->setArticle($article)
                ->setAuthor($user));
        }

        $this->em->flush();

        $response = $this->act(fn () => $this->client->request('GET', '/api/articles/test-title/comments'));

        $this->assertResponseIsSuccessful();

        $this->assertCount(20, $response['comments']);

        $this->assertJsonContains([
            'comments' => [
                0 => [
                    'body' => 'Test Comment 20',
                    'author' => [
                        'username' => 'John Doe',
                        'bio' => 'John Bio',
                        'image' => 'https://randomuser.me/api/portraits/men/1.jpg',
                    ],
                ],
            ],
        ]);
    }
}
