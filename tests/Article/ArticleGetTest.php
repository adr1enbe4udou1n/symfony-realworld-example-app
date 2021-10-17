<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Tests\AbstractTest;

class ArticleGetTest extends AbstractTest
{
    public function testCanGetArticle()
    {
        // $this->em->persist((new Article())
        //     ->setTitle('Test Title')
        //     ->setDescription('Test Description')
        //     ->setBody('Test Body')
        // );
        // $this->em->flush();

        // $this->act(fn () => $this->client->request('POST', '/api/articles', [
        //     'json' => [
        //         'title' => 'Existing Title',
        //         'description' => 'Test Description',
        //         'body' => 'Test Body',
        //     ],
        // ]));

        // $this->assertResponseStatusCodeSame(400);
    }
}
