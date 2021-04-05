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

namespace Productivity\Interfaces\Rest\ApiPlatform\Messenger\Handler;

use Streak\Application\Command;
use Streak\Application\CommandBus;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Simple messenger adapter for Streak command bus.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Handler\CommandDispatchingHandlerTest
 */
final class CommandDispatchingHandler implements MessageHandlerInterface
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(Command $command) : void
    {
        $this->bus->dispatch($command);
    }
}
