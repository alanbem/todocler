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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Productivity\UsersFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Filter\AuthenticatedUserFilter
 */
class AuthenticatedUserFilterTest extends TestCase
{
    private TokenStorageInterface $tokens;
    private TokenInterface $token;
    private UserInterface $user;
    private UsersFacade $facade;
    private EntityManagerInterface $entityManager;
    private QueryNameGeneratorInterface $nameGenerator;

    protected function setUp() : void
    {
        $this->tokens = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->user = $this->createMock(UserInterface::class);
        $this->facade = $this->createMock(UsersFacade::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
    }

    public function testFilter() : void
    {
        $this->entityManager
            ->expects($this->never())
            ->method($this->anything());

        $this->nameGenerator
            ->expects($this->never())
            ->method($this->anything());

        $this->tokens
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $expected = clone $builder;
        $expected->andWhere('ikmngdmx.userId = :userId');
        $expected->setParameter('userId', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');

        $this->assertNotEquals($expected, $builder);

        $filter = new AuthenticatedUserFilter($this->tokens, $this->facade);
        $filter->apply($builder, $this->nameGenerator, \stdClass::class, 'get', []);

        $this->assertSame([], $filter->getDescription(\stdClass::class));
        $this->assertEquals($expected, $builder);
    }

    public function testFilterWhenAuthenticationToken() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->entityManager
            ->expects($this->never())
            ->method($this->anything());

        $this->nameGenerator
            ->expects($this->never())
            ->method($this->anything());

        $this->tokens
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->token
            ->expects($this->never())
            ->method($this->anything());

        $this->user
            ->expects($this->never())
            ->method($this->anything());

        $this->facade
            ->expects($this->never())
            ->method($this->anything());

        $filter = new AuthenticatedUserFilter($this->tokens, $this->facade);
        $filter->apply(new QueryBuilder($this->entityManager), $this->nameGenerator, \stdClass::class, 'get', []);
    }

    public function testFilterWhenNoSecurityUser() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->entityManager
            ->expects($this->never())
            ->method($this->anything());

        $this->nameGenerator
            ->expects($this->never())
            ->method($this->anything());

        $this->tokens
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn('string');

        $this->user
            ->expects($this->never())
            ->method($this->anything());

        $this->facade
            ->expects($this->never())
            ->method($this->anything());

        $filter = new AuthenticatedUserFilter($this->tokens, $this->facade);
        $filter->apply(new QueryBuilder($this->entityManager), $this->nameGenerator, \stdClass::class, 'get', []);
    }

    public function testFilterWhenUserNotRegistered() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->entityManager
            ->expects($this->never())
            ->method($this->anything());

        $this->nameGenerator
            ->expects($this->never())
            ->method($this->anything());

        $this->tokens
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn(null);

        $filter = new AuthenticatedUserFilter($this->tokens, $this->facade);
        $filter->apply(new QueryBuilder($this->entityManager), $this->nameGenerator, \stdClass::class, 'get', []);
    }
}
