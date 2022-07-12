<?php

declare(strict_types=1);

namespace App\Serializer\ContextBuilder;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AdminGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private readonly SerializerContextBuilderInterface $decorated,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $isAdmin = $this->authorizationChecker->isGranted('ROLE_ADMIN');

        if (isset($context['groups']) && $isAdmin) {
            $context['groups'][] = $normalization ? 'admin:read' : 'admin:write';
        }

        return $context;
    }
}
