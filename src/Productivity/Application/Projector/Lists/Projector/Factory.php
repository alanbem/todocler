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

namespace Productivity\Application\Projector\Lists\Projector;

use Doctrine\ORM\EntityManagerInterface;
use Productivity\Application\Projector\Lists;
use Streak\Domain\Event;
use Streak\Domain\Exception\InvalidIdGiven;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Projector\Lists\Projector\FactoryTest
 */
final class Factory implements Event\Listener\Factory
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Event\Listener\Id $id) : Event\Listener
    {
        if (!$id instanceof Lists\Projector\Id) {
            throw new InvalidIdGiven($id);
        }

        return new Lists\Projector($id, $this->manager);
    }

    public function createFor(Event\Envelope $event) : Event\Listener
    {
        return $this->create(new Lists\Projector\Id()); // startup/initialize this projector for any event
    }
}
