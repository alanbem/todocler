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

namespace Productivity\Application\ProcessManager\Registration\ProcessManager;

use Productivity\Application\ProcessManager\Registration;
use Streak\Application\CommandBus;
use Streak\Domain\Event;
use Streak\Domain\Event\Listener;
use Streak\Domain\Exception\InvalidIdGiven;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\ProcessManager\Registration\ProcessManager\FactoryTest
 * @see \Productivity\Application\ProcessManager\Registration\ProcessManager
 */
final class Factory implements Listener\Factory
{
    public function __construct(private CommandBus $bus, private string $name)
    {
    }

    public function create(Event\Listener\Id $id) : Event\Listener
    {
        if (!$id instanceof Registration\ProcessManager\Id) {
            throw new InvalidIdGiven($id);
        }

        return new Registration\ProcessManager($id, $this->bus, $this->name);
    }

    public function createFor(Event\Envelope $event) : Event\Listener
    {
        return $this->create(new Registration\ProcessManager\Id()); // startup/initialize this process manager for any event
    }
}
