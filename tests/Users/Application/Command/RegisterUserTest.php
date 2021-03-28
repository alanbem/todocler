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

namespace Users\Application\Command;

use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Exception\AggregateAlreadyExists;
use Streak\Infrastructure\FixedClock;
use Streak\Infrastructure\Testing\AggregateRoot\TestCase;
use Users\Domain\Event\UserRegistered;
use Users\Domain\PasswordHasher;
use Users\Domain\SaltGenerator;
use Users\Domain\User;
use Users\Infrastructure\SaltGenerator\FixedSaltGenerator;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Command\RegisterUser
 * @covers \Users\Application\Command\RegisterUserHandler
 * @covers \Users\Domain\User
 */
class RegisterUserTest extends TestCase
{
    private PasswordHasher $encoder;
    private SaltGenerator $saltshaker;
    private Clock $clock;

    protected function setUp() : void
    {
        $this->encoder = $this->createMock(PasswordHasher::class);
        $this->saltshaker = new FixedSaltGenerator('salt');
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testRegisteringUser() : void
    {
        $this->encoder
            ->expects($this->once())
            ->method('encode')
            ->with('password')
            ->willReturn('hash');

        $this
            ->for(new User\Id('user-1'))
            ->given()
            ->when(
                new RegisterUser('user-1', 'alan.bem@example.com', 'password'),
            )
            ->then(
                new UserRegistered('user-1', 'alan.bem@example.com', 'hash', 'salt', $this->clock->now()),
            );
    }

    public function testRegisteringUserThatExistsAlready() : void
    {
        $this->expectException(AggregateAlreadyExists::class);
        $this->expectExceptionMessage('Aggregate "Users\Domain\User#user-1" already exists.');

        $this->encoder
            ->expects($this->never())
            ->method($this->anything())
        ;

        $this
            ->for(new User\Id('user-1'))
            ->given(
                new UserRegistered('user-1', 'alan.bem@example.com', 'hash', 'salt', $this->clock->now()),
            )
            ->when(
                new RegisterUser('user-1', 'alan.bem@example.com', 'password'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new RegisterUser('user-1', 'alan.bem@example.com', 'password');

        $this->assertSame('user-1', $command->userId());
        $this->assertSame('alan.bem@example.com', $command->email());
        $this->assertSame('password', $command->password());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new User\Factory($this->encoder, $this->saltshaker, $this->clock);
    }

    protected function createHandler(AggregateRoot\Factory $factory, AggregateRoot\Repository $repository) : CommandHandler
    {
        return new RegisterUserHandler($factory, $repository);
    }
}
