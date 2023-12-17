<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Tag\TagListController;
use App\Dto\Tag\TagResponse;

#[Get(
    name: 'GetTags',
    uriTemplate: '/tags',
    controller: TagListController::class,
    output: TagResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get tags.',
        description: 'Get tags. Auth not required',
        tags: ['Tags'],
        security: [],
    )
)]
class TagResource
{
}
