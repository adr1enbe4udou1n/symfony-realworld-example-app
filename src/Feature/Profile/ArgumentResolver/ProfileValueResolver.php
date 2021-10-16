<?php

namespace App\Feature\Profile\ArgumentResolver;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProfileValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private UserRepository $users,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $request->attributes->has('username') && 'profile' === $argument->getName();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $user = $this->users->findOneBy(['name' => $request->attributes->get('username')]);

        if (!$user) {
            throw new BadRequestHttpException('Not existing profile', null, 404);
        }

        yield $user;
    }
}
