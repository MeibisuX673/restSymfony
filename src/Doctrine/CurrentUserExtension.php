<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;


class CurrentUserExtension implements QueryCollectionExtensionInterface
{

    public function __construct
    (
        private Security $security
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {

        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if (Product::class !== $resourceClass || !$this->security->isGranted('IS_AUTHENTICATED_FULLY') || null == $user) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->leftJoin("$rootAlias.brand", 'b')
            ->andWhere('b.user  = :user')
            ->setParameter('user', $user);

    }

}