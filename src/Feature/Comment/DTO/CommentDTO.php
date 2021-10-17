<?php

namespace App\Feature\Comment\DTO;

use App\Feature\Profile\DTO\ProfileDTO;

class CommentDTO
{
    public int $id;

    public string $body;

    public \DateTime $createdAt;

    public \DateTime $updatedAt;

    public ProfileDTO $author;
}
