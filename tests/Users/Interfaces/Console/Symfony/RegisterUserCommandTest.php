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

namespace Users\Interfaces\Console\Symfony;

use PHPUnit\Framework\TestCase;
use Streak\Application\CommandBus;
use Symfony\Component\Console\Tester\CommandTester;
use Users\Application\Command\RegisterUser;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Interfaces\Console\Symfony\RegisterUserCommand
 */
class RegisterUserCommandTest extends TestCase
{
    private CommandBus $bus;

    protected function setUp() : void
    {
        $this->bus = $this->createMock(CommandBus::class);
    }

    public function testCommand() : void
    {
        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new RegisterUser('21b80428-b0b7-4dab-8a07-d008fe32fe1f', 'alan.bem@example.com', 'password'));

        $command = new RegisterUserCommand($this->bus);

        $tester = new CommandTester($command);
        $tester->execute([
            'id' => '21b80428-b0b7-4dab-8a07-d008fe32fe1f',
            'email' => 'alan.bem@example.com',
            'password' => 'password',
        ]);

        $this->assertSame('User "alan.bem@example.com" registered successfully.'.PHP_EOL, $tester->getDisplay());
    }
}
