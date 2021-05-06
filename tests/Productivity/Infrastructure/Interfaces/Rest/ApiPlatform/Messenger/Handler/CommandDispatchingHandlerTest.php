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

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Handler;

use PHPUnit\Framework\TestCase;
use Streak\Application\CommandBus;
use Streak\Domain\Command;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Handler\CommandDispatchingHandler
 */
final class CommandDispatchingHandlerTest extends TestCase
{
    private CommandBus $bus;
    private Command $command;

    protected function setUp() : void
    {
        $this->bus = $this->createMock(CommandBus::class);
        $this->command = $this->createMock(Command::class);
    }

    public function testHandler() : void
    {
        $this->bus
            ->expects(self::once())
            ->method('dispatch')
            ->with($this->command);

        $handler = new CommandDispatchingHandler($this->bus);
        $handler($this->command);
    }
}
