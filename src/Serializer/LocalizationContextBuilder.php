<?php


namespace App\Serializer;


use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Localization\LocalizationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LocalizationContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated
    ) {}

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (!in_array(LocalizationTrait::class, class_uses($context['resource_class']), true)) {
            return $context;
        }

        $context = $this->addLocalizationGroups($context, $request->getMethod());

        return $context;
    }

    private function addLocalizationGroups(array $context, string $method = 'GET'): array
    {
        if (in_array($method, [Request::METHOD_POST, Request::METHOD_PATCH, Request::METHOD_PUT], true)) {
            $context['groups'][] = 'SetLocaleName';
        }

        $context['groups'][] = 'GetLocaleName';

        return $context;
    }
}
