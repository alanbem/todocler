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

use Productivity\Application\Command\CreateList;
use Productivity\Application\ProcessManager\Registration;
use Streak\Application\CommandBus;
use Streak\Domain\Event;
use Streak\Domain\EventStore;
use Users\Domain\Event\UserRegistered;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\ProcessManager\Registration\ProcessManagerTest
 */
class ProcessManager implements Event\Listener, Event\Picker, Event\Filterer
{
    use Event\Listener\Identifying;
    use Event\Listener\Listening;
    use Event\Listener\Filtering;
    use Event\Listener\Commanding {
        muteCommands as disableSideEffects;
        unmuteCommands as enableSideEffects;
    }

    /**
     * Name of the first list.
     */
    private string $name;

    public function __construct(Registration\ProcessManager\Id $id, CommandBus $bus, string $name)
    {
        $this->identifyBy($id);
        $this->dispatchCommandsVia($bus);

        $this->name = $name;
    }

    /**
     * Creates first list for newly registered user.
     */
    public function onUserRegistered(UserRegistered $event) : void
    {
        $this->bus->dispatch(new CreateList($event->userId(), $this->name, $event->userId()));
    }

    public function pick(EventStore $store) : Event\Envelope
    {
        return $store->stream()->first();
    }
}
