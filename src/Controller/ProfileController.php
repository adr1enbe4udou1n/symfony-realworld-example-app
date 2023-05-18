<?php

namespace App\Controller;

use App\Entity\User;
use App\Feature\Profile\Response\ProfileResponse;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profiles/{username}')]
class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

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
    #[ParamConverter('user', options: ['mapping' => ['username' => 'name']])]
    public function get(User $user): Response
    {
        /** @var User */
        $currentUser = $this->getUser();

        return $this->json(ProfileResponse::make($user, $currentUser));
    }

    #[Route('/follow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
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
    #[ParamConverter('user', options: ['mapping' => ['username' => 'name']])]
    public function follow(User $user): Response
    {
        /** @var User */
        $currentUser = $this->getUser();
        $currentUser->follow($user);
        $this->em->flush();

        return $this->json(ProfileResponse::make($user, $currentUser));
    }

    #[Route('/follow', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
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
    #[ParamConverter('user', options: ['mapping' => ['username' => 'name']])]
    public function unfollow(User $user): Response
    {
        /** @var User */
        $currentUser = $this->getUser();
        $currentUser->unfollow($user);
        $this->em->flush();

        return $this->json(ProfileResponse::make($user, $currentUser));
    }
}
