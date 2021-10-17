<?php

namespace App\Feature\Tag\Action;

use App\Feature\Tag\Response\TagResponse;
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
        $response = new TagResponse();
        $response->tags = $this->tags->list();

        return $response;
    }
}