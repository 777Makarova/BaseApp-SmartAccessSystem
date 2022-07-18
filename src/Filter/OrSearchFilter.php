<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\BlackList;
use Doctrine\ORM\QueryBuilder;

final class OrSearchFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ('orsearch' != $property) {
            return;
        }

        $keys = array_keys($value);
        $keys = explode(',', $keys[0]);

        $value = array_values($value);
        $value = $value[0];
        $orX = $queryBuilder->expr()->orX();

        // FIXIT
        if (BlackList::class === $resourceClass) {
            $queryBuilder
                ->leftJoin('o.user', 'user');
        }

        foreach ($keys as $key) {
            if (BlackList::class === $resourceClass) {
                $orX->add("$key LIKE :value");
            } else {
                $orX->add("o.$key LIKE :value");
            }
            $queryBuilder->setParameter('value', "%$value%");
        }
        $queryBuilder->andWhere($orX);
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["orsearch_$property"] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter for search with "OR" ',
                    'name' => 'orsearch',
                    'type' => 'string',
                ],
            ];
        }

        return $description;
    }
}
