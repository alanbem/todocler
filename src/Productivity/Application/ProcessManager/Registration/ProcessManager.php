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

namespace Productivity\Application\ProcessManager\Registration;

use Productivity\Application\Event as Events;
use Productivity\Application\ProcessManager\Registration;
use Productivity\Domain\Command\CreateList;
use Streak\Application\CommandBus;
use Streak\Domain\Event;
use Streak\Domain\EventStore;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\ProcessManager\Registration\ProcessManagerTest
 */
final class ProcessManager implements Event\Listener, Event\Picker, Event\Filterer
{
    use Event\Listener\Filtering;
    use Event\Listener\Identifying;
    use Event\Listener\Listening;

    public function __construct(Registration\ProcessManager\Id $id, private CommandBus $bus, private string $name)
    {
        $this->identifyBy($id);
    }

    /**
     * Creates first list for newly registered user.
     */
    public function onUserRegistered(Events\UserRegistered $event) : void
    {
        $this->bus->dispatch(new CreateList($event->userId(), $this->name, $event->userId()));
    }

    public function pick(EventStore $store) : Event\Envelope
    {
        return $store->stream()->first();
    }
}
