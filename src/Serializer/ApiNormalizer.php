<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\DataProvider\PartialPaginatorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Serializer\AbstractCollectionNormalizer;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Normalizes collections in the JSON API format.
 */
final class ApiNormalizer extends AbstractCollectionNormalizer
{
    #[Pure]
    public function __construct(ResourceClassResolverInterface $resourceClassResolver, string $pageParameterName, ResourceMetadataFactoryInterface $resourceMetadataFactory = null)
    {
        parent::__construct($resourceClassResolver, $pageParameterName, $resourceMetadataFactory);
    }

    public const FORMAT = 'json';

    /**
     * {@inheritdoc}
     */
    protected function getPaginationData($object, array $context = []): array
    {
        $items = [];

        if (!$object instanceof PartialPaginatorInterface) {
            return $items;
        }
        [$paginator, $paginated, $currentPage, $itemsPerPage, $lastPage, $pageTotalItems, $totalItems] = $this->getPaginationConfig($object, $context);

        if (!$paginator) {
            return $items;
        }

        if (null !== $totalItems) {
            $items['totalItems'] = $totalItems;
        }
        if (null !== $itemsPerPage) {
            $items['itemsPerPage'] = $itemsPerPage;
        }
        $countOfPages = ceil($totalItems / $itemsPerPage);
        if (null !== $countOfPages) {
            $items['countOfPages'] = $countOfPages;
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     */
    protected function getItemsData($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof PartialPaginatorInterface) {
            return $object;
        }
        $data = [
            'items' => [],
        ];

        foreach ($object as $obj) {
            $item = $this->normalizer->normalize($obj, $format, $context);
            $data['items'][] = $item;
        }

        return $data;
    }
}
