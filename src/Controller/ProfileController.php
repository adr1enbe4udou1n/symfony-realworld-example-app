<?php

namespace App\Controller;

use App\Entity\User;
use App\Feature\Profile\Response\ProfileResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profiles/celeb_{username}')]
class ProfileController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetProfileByUsername',
        summary: 'Get a profile.',
        description: 'Get a profile of a user of the system. Auth is optional',
        tags: ['Profile'],
        parameters: [
            new OA\Parameter(
                name: 'username',
                in: 'path',
                description: 'Username of the profile to get',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: ProfileResponse::class)
                )
            ),
        ]
    )]
    public function get(User $username): Response
    {
        return $this->json([]);
    }

    #[Route('/follow', methods: ['POST'])]
    #[OA\Post(
        operationId: 'FollowUserByUsername',
        summary: 'Follow a user.',
        description: 'Follow a user by username',
        tags: ['Profile'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'username',
                in: 'path',
                description: 'Username of the profile you want to follow',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: ProfileResponse::class)
                )
            ),
        ]
    )]
    public function follow(User $username): Response
    {
        return $this->json([]);
    }

    #[Route('/follow', methods: ['DELETE'])]
    #[OA\Delete(
        operationId: 'UnfollowUserByUsername',
        summary: 'Unfollow a user.',
        description: 'Unfollow a user by username',
        tags: ['Profile'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'username',
                in: 'path',
                description: 'Username of the profile you want to unfollow',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: ProfileResponse::class)
                )
            ),
        ]
    )]
    public function unfollow(User $username): Response
    {
        return $this->json([]);
    }
}
