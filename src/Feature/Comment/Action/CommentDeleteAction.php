<?php

namespace App\Feature\Comment\Action;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentDeleteAction extends AbstractController
{
    public function __construct(
        private TokenStorageInterface $token,
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
        private RequestStack $request,
    ) {
    }

    public function __invoke(Comment $data)
    {
        $article = $this->articles->findOneBy(['slug' => $this->request->getCurrentRequest()->attributes->get('slug')]);

        if (!$article) {
            return new JsonResponse('No article of this slug found', 404);
        }

        if ($data->article->id !== $article->id) {
            return new JsonResponse('This comment is not associate with requested article', 400);
        }

        /** @var User */
        $user = $this->token->getToken()->getUser();

        if ($data->author->id !== $user->id && $article->author->id !== $user->id) {
            return new JsonResponse('You cannot delete this comment', 400);
        }

        $this->em->remove($data);
        $this->em->flush();
    }
}
