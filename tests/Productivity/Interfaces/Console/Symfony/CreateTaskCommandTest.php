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

namespace Productivity\Interfaces\Console\Symfony;

use PHPStan\Testing\TestCase;
use Productivity\Domain\Command as Commands;
use Productivity\UsersFacade;
use Streak\Application\CommandBus;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 * @covers \Productivity\Interfaces\Console\Symfony\CreateTaskCommand
 */
final class CreateTaskCommandTest extends TestCase
{
    private CommandBus $commands;
    private UsersFacade $facade;

    protected function setUp() : void
    {
        $this->commands = $this->createMock(CommandBus::class);
        $this->facade = $this->createMock(UsersFacade::class);
    }

    public function testCommand() : void
    {
        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('alan.bem@example.com')
            ->willReturn((object) ['id' => '9e28254d-5ab2-418c-b848-d2f06d301e02', 'email' => 'alan.bem@example.com']);

        $this->commands
            ->expects(self::once())
            ->method('dispatch')
            ->with(new Commands\CreateTask('21b80428-b0b7-4dab-8a07-d008fe32fe1f', 'd95a3219-b15b-480a-835d-d15c0d9414b2', 'Task name', '9e28254d-5ab2-418c-b848-d2f06d301e02'));

        $command = new CreateTaskCommand($this->commands, $this->facade);

        $tester = new CommandTester($command);
        $tester->execute([
            'task-id' => 'd95a3219-b15b-480a-835d-d15c0d9414b2',
            'list-id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'name' => 'Task name',
            'email' => 'alan.bem@example.com',
        ]);

        self::assertSame('Task "Task name" created successfully for "alan.bem@example.com".'.PHP_EOL, $tester->getDisplay());
    }

    public function testCommandIfUserNotRegistered() : void
    {
        $this->expectExceptionMessage('User with given email not found.');

        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('alan.bem@example.com')
            ->willReturn(null);

        $this->commands
            ->expects(self::never())
            ->method('dispatch');

        $command = new CreateTaskCommand($this->commands, $this->facade);

        $tester = new CommandTester($command);
        $tester->execute([
            'task-id' => 'd95a3219-b15b-480a-835d-d15c0d9414b2',
            'list-id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'name' => 'List name',
            'email' => 'alan.bem@example.com',
        ]);
    }
}
