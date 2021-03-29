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
use Productivity\Application\Command as Commands;
use Productivity\UsersFacade;
use Streak\Application\CommandBus;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 * @covers \Productivity\Interfaces\Console\Symfony\CreateListCommand
 */
class CreateListCommandTest extends TestCase
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
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('alan.bem@example.com')
            ->willReturn((object) ['id' => '9e28254d-5ab2-418c-b848-d2f06d301e02', 'email' => 'alan.bem@example.com']);

        $this->commands
            ->expects($this->once())
            ->method('dispatch')
            ->with(new Commands\CreateList('21b80428-b0b7-4dab-8a07-d008fe32fe1f', 'List name', '9e28254d-5ab2-418c-b848-d2f06d301e02'));

        $command = new CreateListCommand($this->commands, $this->facade);

        $tester = new CommandTester($command);
        $tester->execute([
            'list-id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'name' => 'List name',
            'email' => 'alan.bem@example.com',
        ]);

        $this->assertSame('List "List name" created successfully for "alan.bem@example.com".'.PHP_EOL, $tester->getDisplay());
    }

    public function testCommandIfUserNotRegistered() : void
    {
        $this->expectExceptionMessage('User with given email not found.');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('alan.bem@example.com')
            ->willReturn(null);

        $this->commands
            ->expects($this->never())
            ->method('dispatch');

        $command = new CreateListCommand($this->commands, $this->facade);

        $tester = new CommandTester($command);
        $tester->execute([
            'list-id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'name' => 'List name',
            'email' => 'alan.bem@example.com',
        ]);
    }
}
