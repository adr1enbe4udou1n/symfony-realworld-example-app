<?php

namespace App\Tests\Comment;

use App\Tests\AbstractTest;

class CommentListTest extends AbstractTest
{
    public function testCannotListCommentsOfNonExistentArticle()
    {
        $this->actingAs();

        $this->act(fn () => $this->client->request('GET', '/api/articles/test-title/comments'));

        $this->assertResponseStatusCodeSame(404);
    }
}
