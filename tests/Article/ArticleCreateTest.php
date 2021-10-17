<?php

namespace App\Tests\Article;

use App\Entity\Article;
use App\Tests\AbstractTest;

class ArticleCreateTest extends AbstractTest
{
    public function getInvalidData()
    {
        yield [[
            'title' => '',
            'description' => 'Test Description',
            'body' => 'Test Body',
        ]];

        yield [[
            'title' => 'Test Title',
            'description' => '',
            'body' => 'Test Body',
        ]];

        yield [[
            'title' => 'Test Title',
            'description' => 'Test Description',
            'body' => '',
        ]];
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testCannotCreateArticleWithInvalidData($article)
    {
        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => $article,
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testCannotCreateArticleWithSameTitle()
    {
        $this->em->persist((new Article())
            ->setTitle('Existing Title')
            ->setDescription('Test Description')
            ->setBody('Test Body')
        );
        $this->em->flush();

        $this->act(fn () => $this->client->request('POST', '/api/articles', [
            'json' => [
                'title' => 'Existing Title',
                'description' => 'Test Description',
                'body' => 'Test Body',
            ],
        ]));

        $this->assertResponseStatusCodeSame(400);
    }
}
