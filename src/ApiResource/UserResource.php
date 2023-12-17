<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\User\LoginController;
use App\Controller\User\RegisterController;
use App\Controller\User\UserGetController;
use App\Controller\User\UserUpdateController;
use App\Dto\User\LoginUserRequest;
use App\Dto\User\NewUserRequest;
use App\Dto\User\UpdateUserRequest;
use App\Dto\User\UserResponse;

#[Post(
    name: 'CreateUser',
    uriTemplate: '/users',
    controller: RegisterController::class,
    input: NewUserRequest::class,
    output: UserResponse::class,
    validationContext: ['groups' => [NewUserRequest::class, 'validationGroups']],
    openapi: new Operation(
        summary: 'Register a new user.',
        description: 'Register a new user',
        tags: ['User and Authentication'],
        security: [],
    )
)]
#[Post(
    name: 'Login',
    uriTemplate: '/users/login',
    controller: LoginController::class,
    input: LoginUserRequest::class,
    output: UserResponse::class,
    openapi: new Operation(
        summary: 'Existing user login.',
        description: 'Login for existing user',
        tags: ['User and Authentication'],
        security: [],
    )
)]
#[Get(
    name: 'GetCurrentUser',
    uriTemplate: '/user',
    controller: UserGetController::class,
    output: UserResponse::class,
    read: false,
    security: 'is_granted("ROLE_USER")',
    openapi: new Operation(
        summary: 'Get current user.',
        description: 'Gets the currently logged-in user',
        tags: ['User and Authentication'],
        security: [['Bearer' => []]],
    )
)]
#[Put(
    name: 'UpdateCurrentUser',
    uriTemplate: '/user',
    controller: UserUpdateController::class,
    input: UpdateUserRequest::class,
    output: UserResponse::class,
    read: false,
    security: 'is_granted("ROLE_USER")',
    openapi: new Operation(
        summary: 'Update current user.',
        description: 'Updated user information for current user',
        tags: ['User and Authentication'],
    )
)]
class UserResource
{
}
