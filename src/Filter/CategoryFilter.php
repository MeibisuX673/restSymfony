<?php

namespace App\Filter;


use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class CategoryFilter extends AbstractFilter
{

    private iterable $categories = [
        'new',
        'popular'
    ];
    private const DATE_CREATE_COLUMN = 'dateCreate';
    private const TIMES_VIEWED_COLUMN = 'timesViewed';
    private const FILTER_NAME = 'category';
    private int $dayNew = 7;
    private int $thresholdPopular = 2;

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {


        if (!array_key_exists(self::FILTER_NAME, $context['filters'])) {
            return;
        }

        $category = $context['filters'][self::FILTER_NAME];
        $category = mb_strtolower($category);

        if (!in_array($category, $this->categories)) {
            return;
        }

        switch ($category) {
            case 'new':
                $queryBuilder->andWhere(sprintf('DATE_DIFF(CURRENT_DATE(),o.%s) < :day',self::DATE_CREATE_COLUMN))
                            ->setParameter('day',$this->dayNew);
                break;

            case 'popular':
                $queryBuilder->andWhere(sprintf('o.%s >= :threshold',  self::TIMES_VIEWED_COLUMN))
                            ->orderBy(sprintf('o.%s',self::TIMES_VIEWED_COLUMN),'DESC')
                            ->setParameter('threshold',$this->thresholdPopular);
                break;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $description['category'] = [
            'property' => 'category',
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'openapi' => [
                'allowReserved' => false,
                'allowEmptyValue' => true,
                'explode' => false,
            ],
        ];

        return $description;
    }
}