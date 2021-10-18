<?php

namespace App\Feature\Tag\Response;

class TagResponse
{
    /**
     * @var string[]
     */
    public array $tags;

    public function __construct($tags)
    {
        $this->tags = $tags;
    }
}
