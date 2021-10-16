<?php

namespace App\Feature\Profile\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\User;
use App\Feature\Profile\DTO\ProfileDTO;
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
        $output->profile = new ProfileDTO();
        $output->profile->username = $data->name;
        $output->profile->bio = $data->bio;
        $output->profile->image = $data->image;

        if ($token = $this->token->getToken()) {
            /** @var User */
            $user = $token->getUser();
            $output->profile->following = $user->following->contains($data);
        }

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
