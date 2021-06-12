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

namespace Users\Application\Projector\Queue;

use Streak\Domain\Event;
use Streak\Domain\EventStore;
use Users\Domain\Event as Events;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Projector\Queue\ProjectorTest
 */
final class Projector implements Event\Listener, Event\Filterer
{
    use Event\Listener\Identifying;

    public function __construct(Projector\Id $id, private Projector\Queue $queue)
    {
        $this->identifyBy($id);
    }

    /**
     * @see \Productivity\Application\Sensor\UsersEvents\Sensor::processMessage()
     */
    public function onUserRegistered(string $id, Events\UserRegistered $event) : void
    {
        $this->queue->send($id, 'user_registered', [
            'user_id' => $event->userId(),
            'email' => $event->email(),
            'registered_at' => $event->registeredAt()->format('Y-m-d H:i:s.u P'),
        ]);
    }

    public function on(Event\Envelope $envelope) : bool
    {
        $event = $envelope->message();

        if ($event instanceof Events\UserRegistered) {
            $this->onUserRegistered($envelope->uuid()->toString(), $event);

            return true;
        }

        return false;
    }

    public function filter(Event\Stream $stream) : Event\Stream
    {
        return $stream->only(Events\UserRegistered::class);
    }

    public function pick(EventStore $store) : Event\Envelope
    {
        return $store->stream()->first(); // start listening from the first event in event store
    }
}
