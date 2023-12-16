<?php

namespace App\Controller\Article;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Article\NewArticleRequest;
use App\Dto\Article\SingleArticleResponse;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleCreateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articles,
        private TagRepository $tags,
    ) {
    }

    public function __invoke(NewArticleRequest $data, ValidatorInterface $validator)
    {
        $validator->validate($data);

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

        return $this->json(new SingleArticleResponse($article, $user));
    }
}
