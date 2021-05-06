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

namespace Users\Domain\Command;

use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\CommandHandler;
use Streak\Domain\Exception\AggregateAlreadyExists;
use Streak\Infrastructure\Domain\Clock\FixedClock;
use Streak\Infrastructure\Domain\Testing\AggregateRoot\TestCase;
use Users\Application\Command\RegisterUserHandler;
use Users\Domain\Event\UserRegistered;
use Users\Domain\PasswordHasher;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Domain\Command\RegisterUser
 * @covers \Users\Application\Command\RegisterUserHandler
 * @covers \Users\Domain\User
 */
final class RegisterUserTest extends TestCase
{
    private PasswordHasher $encoder;
    private Clock $clock;

    protected function setUp() : void
    {
        $this->encoder = $this->createMock(PasswordHasher::class);
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testRegisteringUser() : void
    {
        $this->encoder
            ->expects(self::once())
            ->method('hash')
            ->with('password')
            ->willReturn('hash');

        $this
            ->for(new User\Id('user-1'))
            ->given()
            ->when(
                new RegisterUser('user-1', 'alan.bem@example.com', 'password'),
            )
            ->then(
                new UserRegistered('user-1', 'alan.bem@example.com', 'hash', $this->clock->now()),
            );
    }

    public function testRegisteringUserThatExistsAlready() : void
    {
        $this->expectException(AggregateAlreadyExists::class);
        $this->expectExceptionMessage('Aggregate "Users\Domain\User#user-1" already exists.');

        $this->encoder
            ->expects(self::never())
            ->method(self::anything())
        ;

        $this
            ->for(new User\Id('user-1'))
            ->given(
                new UserRegistered('user-1', 'alan.bem@example.com', 'hash', $this->clock->now()),
            )
            ->when(
                new RegisterUser('user-1', 'alan.bem@example.com', 'password'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new RegisterUser('user-1', 'alan.bem@example.com', 'password');

        self::assertSame('user-1', $command->userId());
        self::assertSame('alan.bem@example.com', $command->email());
        self::assertSame('password', $command->password());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new User\Factory($this->encoder, $this->clock);
    }

    protected function createHandler(AggregateRoot\Factory $factory, AggregateRoot\Repository $repository) : CommandHandler
    {
        return new RegisterUserHandler($factory, $repository);
    }
}
