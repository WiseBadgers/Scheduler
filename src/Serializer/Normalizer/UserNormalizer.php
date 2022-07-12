<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private readonly Security $security
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($this->userIsOwner($object)) {
            $context['groups'][] = 'owner:read';
        }
        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        // avoid recursion: only call once per object
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }

    private function userIsOwner(User $user): bool
    {
        /** @var User|null $authUser */
        $authUser = $this->security->getUser();

        if (!$authUser) {
            return false;
        }

        return $authUser->getEmail() === $user->getEmail();
    }
}
