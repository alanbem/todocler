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

namespace Productivity\Infrastructure\Interfaces\Console\Symfony;

use PHPStan\Testing\TestCase;
use Productivity\Domain\Command as Commands;
use Productivity\UsersFacade;
use Streak\Application\CommandBus;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Infrastructure\Interfaces\Console\Symfony\CreateProjectCommand
 */
final class CreateProjectCommandTest extends TestCase
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
            ->with(new Commands\CreateProject('21b80428-b0b7-4dab-8a07-d008fe32fe1f', 'Project name', '9e28254d-5ab2-418c-b848-d2f06d301e02'));

        $command = new CreateProjectCommand($this->commands, $this->facade);

        $tester = new CommandTester($command);
        $tester->execute([
            'project-id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'name' => 'Project name',
            'email' => 'alan.bem@example.com',
        ]);

        self::assertSame('Project "Project name" created successfully for "alan.bem@example.com".'.PHP_EOL, $tester->getDisplay());
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

        $command = new CreateProjectCommand($this->commands, $this->facade);

        $tester = new CommandTester($command);
        $tester->execute([
            'project-id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'name' => 'Project name',
            'email' => 'alan.bem@example.com',
        ]);
    }
}
