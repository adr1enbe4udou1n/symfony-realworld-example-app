<?php

namespace App\Feature\Tag\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Feature\Tag\Response\TagResponse;

final class TagsDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $data
     */
    public function transform($data, string $to, array $context = []): TagResponse
    {
        $output = new TagResponse();
        $output->tags = ['Tag1', 'Tag2', 'Tag3'];

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return TagResponse::class === $to;
    }
}
