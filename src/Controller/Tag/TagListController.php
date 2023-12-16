<?php

namespace App\Controller\Tag;

use App\Dto\Tag\TagResponse;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagListController extends AbstractController
{
    public function __construct(
        private TagRepository $tags,
    ) {
    }

    public function __invoke()
    {
        return $this->json(new TagResponse($this->tags->list()));
    }
}
