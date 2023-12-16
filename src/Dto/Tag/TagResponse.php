<?php

namespace App\Dto\Tag;

class TagResponse
{
    public function __construct(
        public array $tags,
    ) {
    }
}
