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

namespace Users\Infrastructure;

use PHPUnit\Framework\TestCase;
use Streak\Application\QueryBus;
use Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser;
use Users\Application\Query as Queries;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\UsersFacadeForProductivity
 */
class UsersFacadeForProductivityTest extends TestCase
{
    private QueryBus $bus;

    protected function setUp() : void
    {
        $this->bus = $this->createMock(QueryBus::class);
    }

    public function testIsUserRegistered() : void
    {
        $facade = new UsersFacadeForProductivity($this->bus);

        $this->bus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [new Queries\IsUserRegistered('john.doe@example.com')],
                [new Queries\IsUserRegistered('me@example.com')],
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true,
            );

        $this->assertFalse($facade->isUserRegistered('john.doe@example.com'));
        $this->assertTrue($facade->isUserRegistered('me@example.com'));
    }

    public function testIsUserRegisteredWhenQueryFails() : void
    {
        $facade = new UsersFacadeForProductivity($this->bus);

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new Queries\IsUserRegistered('john.doe@example.com'))
            ->willThrowException(new \RuntimeException('test'));

        $this->assertFalse($facade->isUserRegistered('john.doe@example.com'));
    }

    public function testFindRegisteredUser() : void
    {
        $facade = new UsersFacadeForProductivity($this->bus);

        $this->bus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [new Queries\FindUser('john.doe@example.com')],
                [new Queries\FindUser('jane.doe@example.com')],
            )
            ->willReturnOnConsecutiveCalls(
                null,
                new RegisteredUser('03913d93-4ea0-4718-a4e5-0c26b4607617', 'jane.doe@example.com', 'password', 'salt', new \DateTimeImmutable()),
            );

        $this->assertNull($facade->findRegisteredUser('john.doe@example.com'));
        $this->assertEquals((object) ['id' => '03913d93-4ea0-4718-a4e5-0c26b4607617', 'email' => 'jane.doe@example.com'], $facade->findRegisteredUser('jane.doe@example.com'));
    }

    public function testFindRegisteredUserWhenQueryFails() : void
    {
        $facade = new UsersFacadeForProductivity($this->bus);

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new Queries\FindUser('john.doe@example.com'))
            ->willThrowException(new \RuntimeException('test'));

        $this->assertNull($facade->findRegisteredUser('john.doe@example.com'));
    }
}
