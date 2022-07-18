<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User\User;
use App\Serializer\User\UserContextBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ContextBuilder implements SerializerContextBuilderInterface
{
    public const BUILDERS = [
        User::class => UserContextBuilder::class,
    ];

    public function __construct(
        private ContainerInterface $container,
        private SerializerContextBuilderInterface $decorated
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        $itemOperationName = $context['item_operation_name'] ?? null;
        $collectionOperationName = $context['collection_operation_name'] ?? null;
        $operationType = $context['operation_type'] ?? null;

        if (!isset(self::BUILDERS[$resourceClass])) {
            return $context;
        }

        if ('item' === $operationType) {
            $itemGroups = $context['item_groups'] ?? [];
            $context['groups'] = array_merge($context['groups'], $itemGroups);
        }

        $addGroups = $context['add_groups'] ?? [];
        if ($addGroups) {
            $context['groups'] = array_merge($context['groups'], $addGroups);
        }

        /** @var SerializerContextBuilderInterface $builder */
        $builder = $this->container->get(self::BUILDERS[$resourceClass]);
        $builder->createFromRequest($request, $normalization);

        return $context;
    }
}
