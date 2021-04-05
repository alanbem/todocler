<?php

/**
 * This file is part of the todocler package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Productivity\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension;

/*
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Productivity\UsersFacade;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * By convention this extension filter all resources if they have property called "userId".
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension\AuthenticatedUserExtensionTest
 */
final class AuthenticatedUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private const USER_ID_PROPERTY_NAME = 'userId'; // @TODO: parametrize

    private Security $security;
    private UsersFacade $users;

    public function __construct(Security $security, UsersFacade $users)
    {
        $this->security = $security;
        $this->users = $users;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null) : void
    {
        $this->build($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []) : void
    {
        $this->build($queryBuilder, $resourceClass);
    }

    private function build(QueryBuilder $queryBuilder, string $resourceClass) : void
    {
        $reflection = new \ReflectionClass($resourceClass);

        if (false === $reflection->hasProperty(self::USER_ID_PROPERTY_NAME)) {
            return;
        }

        $id = $this->authenticatedUserId();

        $alias = $queryBuilder->getRootAliases()[0];
        // convention here: if there a field userId we assume its an owner that we filter by
        $queryBuilder->andWhere(sprintf('%s.%s = :authenticatedUser', $alias, self::USER_ID_PROPERTY_NAME));
        $queryBuilder->setParameter('authenticatedUser', $id);
    }

    private function authenticatedUserId() : string
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        $user = $this->users->findRegisteredUser($user->getUsername());

        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user->id;
    }
}
