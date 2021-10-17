<?php

namespace App\Feature\Comment\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Comment;
use App\Entity\User;
use App\Feature\Comment\Request\NewCommentRequest;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class NewCommentDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private ArticleRepository $articles,
        private ValidatorInterface $validator,
        private TokenStorageInterface $token,
        private RequestStack $request,
    ) {
    }

    /**
     * @param NewCommentRequest $data
     */
    public function transform($data, string $to, array $context = []): Comment
    {
        $article = $this->articles->findOneBy(['slug' => $this->request->getCurrentRequest()->attributes->get('slug')]);

        if (!$article) {
            throw new NotFoundHttpException('Not existing article');
        }

        $this->validator->validate($data->comment);

        /** @var User */
        $user = $this->token->getToken()->getUser();

        $comment = new Comment();
        $comment->body = $data->comment->body;
        $comment->author = $user;
        $comment->article = $article;

        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Comment) {
            return false;
        }

        return NewCommentRequest::class === ($context['input']['class'] ?? null);
    }
}
