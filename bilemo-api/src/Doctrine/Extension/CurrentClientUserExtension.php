<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User as AppUser;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Metadata\Operation;

/**
 * Restrict User resources to the authenticated Client (owner).
 */
class CurrentClientUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $qb, string $resourceClass): void
    {
        if (AppUser::class !== $resourceClass) {
            return;
        }

        $client = $this->security->getUser();
        if (!$client) {
            // Anonymous: let the firewall/security layer deny access
            return;
        }

        $rootAlias = $qb->getRootAliases()[0];
        $qb->andWhere(sprintf('%s.client = :current_client', $rootAlias))
           ->setParameter('current_client', $client);
    }
}
