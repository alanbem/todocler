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

namespace Users\Application\Projector\RegisteredUsers\Projector;

use Doctrine\ORM\EntityManagerInterface;
use Streak\Domain\Event;
use Streak\Domain\Exception\InvalidIdGiven;
use Users\Application\Projector\RegisteredUsers;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Projector\RegisteredUsers\Projector\FactoryTest
 */
class Factory implements Event\Listener\Factory
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Event\Listener\Id $id) : Event\Listener
    {
        if (!$id instanceof RegisteredUsers\Projector\Id) {
            throw new InvalidIdGiven($id);
        }

        return new RegisteredUsers\Projector($id, $this->manager);
    }

    public function createFor(Event\Envelope $event) : Event\Listener
    {
        return $this->create(new RegisteredUsers\Projector\Id()); // startup/initialize this projector for any event
    }
}
