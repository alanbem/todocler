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

namespace Users\Application\Projector\Queue\Projector;

use Streak\Domain\Event;
use Streak\Domain\Exception\InvalidIdGiven;
use Users\Application\Projector\Queue\Projector;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Projector\RegisteredUsers\Projector\FactoryTest
 * @see \Users\Application\Projector\Queue\Projector\FactoryTest
 */
final class Factory implements Event\Listener\Factory
{
    private Projector\Queue $queue;

    public function __construct(Projector\Queue $queue)
    {
        $this->queue = $queue;
    }

    public function create(Event\Listener\Id $id) : Event\Listener
    {
        if (!$id instanceof Projector\Id) {
            throw new InvalidIdGiven($id);
        }

        return new Projector($id, $this->queue);
    }

    public function createFor(Event\Envelope $event) : Event\Listener
    {
        return $this->create(new Projector\Id()); // startup/initialize this projector for any event
    }
}
