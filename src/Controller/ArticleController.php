<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Feature\Article\Request\NewArticleRequest;
use App\Feature\Article\Request\UpdateArticleRequest;
use App\Feature\Article\Response\MultipleArticlesResponse;
use App\Feature\Article\Response\SingleArticleResponse;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articles,
        private TagRepository $tags,
        private TokenStorageInterface $token,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetArticles',
        summary: 'Get recent articles globally.',
        description: 'Get most recent articles globally. Use query parameters to filter results. Auth is optional',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Limit number of articles returned (default is 20)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset/skip number of articles (default is 0)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'author',
                in: 'query',
                description: 'Filter by author (username)',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'favorited',
                in: 'query',
                description: 'Filter by favorites of a user (username)',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'tag',
                in: 'query',
                description: 'Filter by tag',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: MultipleArticlesResponse::class)
                )
            ),
        ]
    )]
    public function list(Request $request): Response
    {
        $paginator = $this->articles->list(
            (int) $request->query->get('limit', (string) ArticleRepository::MAX_ITEMS_PER_PAGE),
            (int) $request->query->get('offset', '0'),
            $request->query->get('author'),
            $request->query->get('tag'),
            $request->query->get('favorited'),
        );

        return $this->json(
            MultipleArticlesResponse::make(
                $paginator->getIterator()->getArrayCopy(), $paginator->count(), $this->token
            )
        );
    }

    #[Route('/feed', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        operationId: 'GetArticlesFeed',
        summary: 'Get recent articles from users you follow.',
        description: 'Get most recent articles from users you follow. Use query parameters to limit. Auth is required',
        tags: ['Articles'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Limit number of articles returned (default is 20)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset/skip number of articles (default is 0)',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: MultipleArticlesResponse::class)
                )
            ),
        ]
    )]
    public function feed(Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();

        $paginator = $this->articles->feed(
            $user,
            (int) $request->query->get('limit', (string) ArticleRepository::MAX_ITEMS_PER_PAGE),
            (int) $request->query->get('offset', '0'),
        );

        return $this->json(
            MultipleArticlesResponse::make(
                $paginator->getIterator()->getArrayCopy(), $paginator->count(), $this->token
            )
        );
    }

    #[Route('/{slug}', methods: ['GET'])]
    #[OA\Get(
        operationId: 'GetArticle',
        summary: 'Get an article',
        description: 'Get an article. Auth not required',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article to get',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function get(Article $article): Response
    {
        return $this->json(SingleArticleResponse::make($article, $this->token));
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Post(
        operationId: 'CreateArticle',
        summary: 'Create an article.',
        description: 'Create an article. Auth is required',
        tags: ['Articles'],
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: NewArticleRequest::class)
            )
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    #[ParamConverter('data', converter: 'fos_rest.request_body')]
    public function create(NewArticleRequest $data, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, 422);
        }

        if ($this->articles->findOneBy(['title' => $data->article->title])) {
            return $this->json(['message' => 'Article with this title already exist'], 400);
        }

        /** @var User */
        $user = $this->getUser();

        $article = new Article();
        $article->title = $data->article->title;
        $article->description = $data->article->description;
        $article->body = $data->article->body;
        $article->author = $user;

        $existingTags = $this->tags->byNames($data->article->tagList);

        foreach ($data->article->tagList as $tagName) {
            $tags = array_filter($existingTags, fn (Tag $t) => $t->name === $tagName);

            if (empty($tags)) {
                $article->addTag((new Tag())->setName($tagName));
                continue;
            }

            $article->addTag(reset($tags));
        }

        $this->em->persist($article);
        $this->em->flush();

        return $this->json(SingleArticleResponse::make($article, $this->token));
    }

    #[Route('/{slug}', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Put(
        operationId: 'UpdateArticle',
        summary: 'Update an article.',
        description: 'Update an article. Auth is required',
        tags: ['Articles'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article to update',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: UpdateArticleRequest::class)
            )
        ),
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    #[ParamConverter('data', converter: 'fos_rest.request_body')]
    public function update(Article $article, UpdateArticleRequest $data, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, 422);
        }

        if (
            ($existingArticle = $this->articles->findOneBy(['title' => $data->article->title])) &&
            $existingArticle->id !== $article->id
        ) {
            return $this->json(['message' => 'Article with this title already exist'], 400);
        }

        if ($this->getUser()->getUserIdentifier() !== $article->author->getUserIdentifier()) {
            return $this->json(['message' => 'You cannot not edit article of other authors'], 400);
        }

        $article->title = $data->article->title ?? $article->title;
        $article->description = $data->article->description ?? $article->description;
        $article->body = $data->article->body ?? $article->body;

        $this->em->persist($article);
        $this->em->flush();

        return $this->json(SingleArticleResponse::make($article, $this->token));
    }

    #[Route('/{slug}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Delete(
        operationId: 'DeleteArticle',
        summary: 'Delete an article.',
        description: 'Delete an article. Auth is required',
        tags: ['Articles'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article to delete',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function delete(Article $article): Response
    {
        /** @var User */
        $user = $this->getUser();

        if ($article->author->id !== $user->id) {
            return $this->json(['message' => 'You cannot delete this article'], 400);
        }

        $this->em->remove($article);
        $this->em->flush();

        return $this->json([])->setStatusCode(204);
    }

    #[Route('/{slug}/favorite', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Post(
        operationId: 'CreateArticleFavorite',
        summary: 'Favorite an article.',
        description: 'Favorite an article. Auth is required',
        tags: ['Favorites'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to favorite',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function favorite(Article $article): Response
    {
        /** @var User */
        $user = $this->getUser();

        $user->favorite($article);
        $this->em->flush();

        return $this->json(SingleArticleResponse::make($article, $this->token));
    }

    #[Route('/{slug}/favorite', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Delete(
        operationId: 'DeleteArticleFavorite',
        summary: 'Unfavorite an article.',
        description: 'Unfavorite an article. Auth is required',
        tags: ['Favorites'],
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the article that you want to unfavorite',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            '200' => new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    ref: new Model(type: SingleArticleResponse::class)
                )
            ),
        ]
    )]
    public function unfavorite(Article $article): Response
    {
        /** @var User */
        $user = $this->getUser();

        $user->unfavorite($article);
        $this->em->flush();

        return $this->json(SingleArticleResponse::make($article, $this->token));
    }
}
