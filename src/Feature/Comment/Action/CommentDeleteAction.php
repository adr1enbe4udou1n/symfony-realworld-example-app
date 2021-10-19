<?php

namespace App\Feature\Comment\Action;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentDeleteAction extends AbstractController
{
    public function __invoke(Comment $data, TokenStorageInterface $token, EntityManagerInterface $em, ArticleRepository $articles, Request $request)
    {
        $article = $articles->findOneBy(['slug' => $request->attributes->get('slug')]);

        if (!$article) {
            return new JsonResponse('No article of this slug found', 404);
        }

        if ($data->article->id !== $article->id) {
            return new JsonResponse('This comment is not associate with requested article', 400);
        }

        /** @var User */
        $user = $token->getToken()->getUser();

        if ($data->author->id !== $user->id && $article->author->id !== $user->id) {
            return new JsonResponse('You cannot delete this comment', 400);
        }

        $em->remove($data);
        $em->flush();
    }
}
