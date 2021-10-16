<?php

namespace App\Feature\Profile\DTO;

class ProfileDTO
{
    public ?string $username = null;

    public ?string $bio = null;

    public ?string $image = null;

    public bool $following = false;
}
