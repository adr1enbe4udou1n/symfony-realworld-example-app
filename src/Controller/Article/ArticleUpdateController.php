<?php

namespace App\Controller\Article;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Article\SingleArticleResponse;
use App\Dto\Article\UpdateArticleRequest;
use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleUpdateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
    ) {
    }

    public function __invoke(Article $article, UpdateArticleRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

        if (
            ($existingArticle = $this->articles->findOneBy(['title' => $data->article->title]))
            && $existingArticle->id !== $article->id
        ) {
            return $this->json(['message' => 'Article with this title already exist'], 400);
        }

        /** @var User */
        $user = $this->getUser();

        if ($user->id !== $article->author->id) {
            return $this->json(['message' => 'You cannot not edit article of other authors'], 400);
        }

        $article->title = $data->article->title ?? $article->title;
        $article->description = $data->article->description ?? $article->description;
        $article->body = $data->article->body ?? $article->body;

        $this->em->persist($article);
        $this->em->flush();

        return $this->json(new SingleArticleResponse($article, $user));
    }
}
