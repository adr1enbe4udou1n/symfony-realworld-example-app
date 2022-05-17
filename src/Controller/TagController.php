<?php

namespace App\Controller;

use App\Feature\Tag\Response\TagResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tags')]
class TagController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetTags',
        summary: 'Get tags.',
        description: 'Get tags. Auth not required',
        tags: ['Tags'],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: TagResponse::class)
                )
            ),
        ]
    )]
    public function list(): Response
    {
        return $this->json([]);
    }
}
