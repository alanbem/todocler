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

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension\AuthenticatedUserExtensionTest\ResourceWithoutUserIdAttribute;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension\AuthenticatedUserExtensionTest\ResourceWithUserIdAttribute;
use Productivity\UsersFacade;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension\AuthenticatedUserExtension
 */
final class AuthenticatedUserExtensionTest extends TestCase
{
    private Security $security;
    private UserInterface $user;
    private UsersFacade $facade;
    private EntityManagerInterface $entityManager;
    private QueryNameGeneratorInterface $nameGenerator;

    protected function setUp() : void
    {
        $this->security = $this->createMock(Security::class);
        $this->user = $this->getMockBuilder(UserInterface::class)->addMethods(['getUserIdentifier'])->getMockForAbstractClass();
        $this->facade = $this->createMock(UsersFacade::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
    }

    public function testApplyingToCollection() : void
    {
        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $expected = clone $builder;
        $expected->andWhere('ikmngdmx.userId = :authenticatedUser');
        $expected->setParameter('authenticatedUser', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');

        self::assertNotEquals($expected, $builder);

        $extension->applyToCollection($builder, $this->nameGenerator, ResourceWithUserIdAttribute::class, null);

        self::assertEquals($expected, $builder);
    }

    public function testApplyingToItem() : void
    {
        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $expected = clone $builder;
        $expected->andWhere('ikmngdmx.userId = :authenticatedUser');
        $expected->setParameter('authenticatedUser', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');

        self::assertNotEquals($expected, $builder);

        $extension->applyToItem($builder, $this->nameGenerator, ResourceWithUserIdAttribute::class, [], '', []);

        self::assertEquals($expected, $builder);
    }

    public function testApplyingToCollectionOfResourcesWithoutUserIdAttribute() : void
    {
        $this->security
            ->expects(self::never())
            ->method(self::anything());

        $this->user
            ->expects(self::never())
            ->method(self::anything());

        $this->facade
            ->expects(self::never())
            ->method(self::anything());

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $expected = clone $builder;

        $extension->applyToCollection($builder, $this->nameGenerator, ResourceWithoutUserIdAttribute::class, null);

        self::assertEquals($expected, $builder);
    }

    public function testApplyingToResourceWithoutUserIdAttribute() : void
    {
        $this->security
            ->expects(self::never())
            ->method(self::anything());

        $this->user
            ->expects(self::never())
            ->method(self::anything());

        $this->facade
            ->expects(self::never())
            ->method(self::anything());

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $expected = clone $builder;

        $extension->applyToItem($builder, $this->nameGenerator, ResourceWithoutUserIdAttribute::class, [], '', []);

        self::assertEquals($expected, $builder);
    }

    public function testApplyingToCollectionWhenUserNotAuthenticated() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $this->user
            ->expects(self::never())
            ->method(self::anything());

        $this->facade
            ->expects(self::never())
            ->method(self::anything());

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $extension->applyToCollection($builder, $this->nameGenerator, ResourceWithUserIdAttribute::class, null);
    }

    public function testApplyingToItemWhenUserNotAuthenticated() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $this->user
            ->expects(self::never())
            ->method(self::anything());

        $this->facade
            ->expects(self::never())
            ->method(self::anything());

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $extension->applyToItem($builder, $this->nameGenerator, ResourceWithUserIdAttribute::class, [], '', []);
    }

    public function testApplyingToCollectionWhenUserNotRegistered() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn(null);

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $extension->applyToCollection($builder, $this->nameGenerator, ResourceWithUserIdAttribute::class, null);
    }

    public function testApplyingToItemWhenUserNotRegistered() : void
    {
        $this->expectExceptionObject(new AccessDeniedException());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn(null);

        $extension = new AuthenticatedUserExtension($this->security, $this->facade);

        $builder = new QueryBuilder($this->entityManager);
        $builder->from('table', 'ikmngdmx');

        $extension->applyToItem($builder, $this->nameGenerator, ResourceWithUserIdAttribute::class, [], '', []);
    }
}

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Doctrine\Orm\Extension\AuthenticatedUserExtensionTest;

final class ResourceWithUserIdAttribute
{
    private string $userId;
}

final class ResourceWithoutUserIdAttribute
{
}
