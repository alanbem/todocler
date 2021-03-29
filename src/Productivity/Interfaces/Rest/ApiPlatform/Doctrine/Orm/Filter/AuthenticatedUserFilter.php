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

namespace Productivity\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Productivity\UsersFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Filter\AuthenticatedUserFilterTest
 */
final class AuthenticatedUserFilter implements FilterInterface
{
    const USER_ID_FIELD = 'userId'; // maybe parametrize later on

    private TokenStorageInterface $tokens;
    private UsersFacade $users;

    public function __construct(TokenStorageInterface $tokens, UsersFacade $users)
    {
        $this->tokens = $tokens;
        $this->users = $users;
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->authenticatedUser();
        $user = $this->users->findRegisteredUser($user->getUsername());

        if (null === $user) {
            throw new AccessDeniedException();
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.%s = :%s', $alias, self::USER_ID_FIELD, self::USER_ID_FIELD));
        $queryBuilder->setParameter(self::USER_ID_FIELD, $user->id);
    }

    public function getDescription(string $resourceClass) : array
    {
        return [];
    }

    private function authenticatedUser() : UserInterface
    {
        $token = $this->tokens->getToken();

        if (null === $token) {
            throw new AccessDeniedException();
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
