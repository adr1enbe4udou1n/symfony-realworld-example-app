<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\Profile\ProfileFollowController;
use App\Controller\Profile\ProfileGetController;
use App\Controller\Profile\ProfileUnfollowController;
use App\Dto\Profile\ProfileResponse;

#[Get(
    name: 'GetProfileByUsername',
    uriTemplate: '/profiles/{username}',
    controller: ProfileGetController::class,
    output: ProfileResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get a profile.',
        description: 'Get a profile of a user of the system. Auth is optional',
        tags: ['Profile'],
        security: [],
        parameters: [
            new Parameter(
                name: 'username',
                in: 'path',
                required: true,
                description: 'Username of the profile to get',
            ),
        ],
    )
)]
#[Post(
    name: 'FollowUserByUsername',
    uriTemplate: '/profiles/{username}/follow',
    controller: ProfileFollowController::class,
    security: 'is_granted("ROLE_USER")',
    input: false,
    output: ProfileResponse::class,
    openapi: new Operation(
        summary: 'Follow a user.',
        description: 'Follow a user by username',
        tags: ['Profile'],
        parameters: [
            new Parameter(
                name: 'username',
                in: 'path',
                required: true,
                description: 'Username of the profile you want to follow',
            ),
        ],
    )
)]
#[Delete(
    name: 'UnfollowUserByUsername',
    uriTemplate: '/profiles/{username}/follow',
    controller: ProfileUnfollowController::class,
    security: 'is_granted("ROLE_USER")',
    output: ProfileResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Unfollow a user.',
        description: 'Unfollow a user by username',
        tags: ['Profile'],
        parameters: [
            new Parameter(
                name: 'username',
                in: 'path',
                required: true,
                description: 'Username of the profile you want to unfollow',
            ),
        ],
    )
)]
class ProfileResource
{
}
