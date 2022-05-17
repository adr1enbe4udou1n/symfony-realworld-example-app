<?php

namespace App\Feature\Tag\Response;

class TagResponse
{
    /**
     * @var array<string>
     */
    public array $tags;

    public static function make(array $tags)
    {
        $response = new self();
        $response->tags = $tags;

        return $response;
    }
}
