<?php

namespace App\EntityListener;

use App\Entity\Article;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleEntityListener
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    public function prePersist(Article $article, LifecycleEventArgs $event)
    {
        $article->computeSlug($this->slugger);
    }

    public function preUpdate(Article $article, LifecycleEventArgs $event)
    {
        $article->computeSlug($this->slugger);
    }
}
