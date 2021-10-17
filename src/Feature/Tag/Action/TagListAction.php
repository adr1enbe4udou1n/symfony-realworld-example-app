<?php

namespace App\Feature\Tag\Action;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagListAction extends AbstractController
{
    public function __construct(
        private TagRepository $tags,
    ) {
    }

    public function __invoke()
    {
        return $this->tags->list();
    }
}
