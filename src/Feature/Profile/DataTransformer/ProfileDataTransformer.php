<?php

namespace App\Feature\Profile\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\User;
use App\Feature\Profile\Response\ProfileResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ProfileDataTransformer implements DataTransformerInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    /**
     * @param User $data
     */
    public function transform($data, string $to, array $context = []): ProfileResponse
    {
        $output = new ProfileResponse();
        $output->profile = $data->getProfile($this->token);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ProfileResponse::class === $to && $data instanceof User;
    }
}
