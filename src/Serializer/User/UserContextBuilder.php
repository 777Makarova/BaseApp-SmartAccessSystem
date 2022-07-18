<?php

namespace App\Serializer\User;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

final class UserContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        //$context = $this->forAdmin($context);
        //$context = $this->forUser($context);

        return $context;
    }

    private function forAdmin($context)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $context;
        }

        return $this->setFullAccess($context);
    }

    private function forUser($context)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $context;
        }

        return $context;
    }

    private function setFullAccess($context)
    {
        $context['groups'] = array_merge($context['groups'], [
            'GetPersonalInfo',
        ]);

        if (in_array('setUser', $context['groups'])) {
            $context['groups'][] = 'setUser:admin';
        }

        if (in_array('updateUser', $context['groups'])) {
            $context['groups'][] = 'updateUser:admin';
        }

        return $context;
    }
}
