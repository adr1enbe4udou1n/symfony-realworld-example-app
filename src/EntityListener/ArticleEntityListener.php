<?php

namespace App\EntityListener;

use App\Entity\Article;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleEntityListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
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
