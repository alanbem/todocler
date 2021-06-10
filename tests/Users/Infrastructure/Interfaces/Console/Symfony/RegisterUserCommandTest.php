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

namespace Users\Infrastructure\Interfaces\Console\Symfony;

use PHPUnit\Framework\TestCase;
use Streak\Application\CommandBus;
use Streak\Application\QueryBus;
use Symfony\Component\Console\Tester\CommandTester;
use Users\Application\Query as Queries;
use Users\Domain\Command as Commands;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\Interfaces\Console\Symfony\RegisterUserCommand
 */
final class RegisterUserCommandTest extends TestCase
{
    private CommandBus $commands;
    private QueryBus $queries;

    protected function setUp() : void
    {
        $this->commands = $this->createMock(CommandBus::class);
        $this->queries = $this->createMock(QueryBus::class);
    }

    public function testCommand() : void
    {
        $this->queries
            ->expects(self::once())
            ->method('dispatch')
            ->with(new Queries\IsUserRegistered('alan.bem@example.com'))
            ->willReturn(false);

        $this->commands
            ->expects(self::once())
            ->method('dispatch')
            ->with(new Commands\RegisterUser('21b80428-b0b7-4dab-8a07-d008fe32fe1f', 'alan.bem@example.com', 'password'));

        $command = new RegisterUserCommand($this->commands, $this->queries);

        $tester = new CommandTester($command);
        $tester->execute([
            'id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'email' => 'alan.bem@example.com',
            'password' => 'password',
        ]);

        self::assertSame('User "alan.bem@example.com" registered successfully.'.PHP_EOL, $tester->getDisplay());
        self::assertSame(0, $tester->getStatusCode());
    }

    public function testCommandWhenQueryFails() : void
    {
        $this->queries
            ->expects(self::once())
            ->method('dispatch')
            ->with(new Queries\IsUserRegistered('alan.bem@example.com'))
            ->willThrowException(new \RuntimeException('test'));

        $this->commands
            ->expects(self::once())
            ->method('dispatch')
            ->with(new Commands\RegisterUser('21b80428-b0b7-4dab-8a07-d008fe32fe1f', 'alan.bem@example.com', 'password'));

        $command = new RegisterUserCommand($this->commands, $this->queries);

        $tester = new CommandTester($command);
        $tester->execute([
            'id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'email' => 'alan.bem@example.com',
            'password' => 'password',
        ]);

        self::assertSame('User "alan.bem@example.com" registered successfully.'.PHP_EOL, $tester->getDisplay());
        self::assertSame(0, $tester->getStatusCode());
    }

    public function testCommandIfUserAlreadyRegistered() : void
    {
        $this->queries
            ->expects(self::once())
            ->method('dispatch')
            ->with(new Queries\IsUserRegistered('alan.bem@example.com'))
            ->willReturn(true);

        $this->commands
            ->expects(self::never())
            ->method('dispatch');

        $command = new RegisterUserCommand($this->commands, $this->queries);

        $tester = new CommandTester($command);
        $tester->execute([
            'id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'email' => 'alan.bem@example.com',
            'password' => 'password',
        ]);

        self::assertSame('User with given email is already registered.'.PHP_EOL, $tester->getDisplay());
        self::assertSame(1, $tester->getStatusCode());
    }
}
