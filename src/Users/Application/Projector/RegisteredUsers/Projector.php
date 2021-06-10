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

namespace Users\Application\Projector\RegisteredUsers;

use Doctrine\ORM\EntityManagerInterface;
use Shared\Application\Projector\Doctrine;
use Streak\Domain\Event;
use Streak\Domain\EventStore;
use Streak\Domain\Query;
use Streak\Domain\QueryHandler;
use Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser;
use Users\Application\Query as Queries;
use Users\Domain\Event as Events;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Projector\RegisteredUsers\ProjectorTest
 */
final class Projector extends Doctrine\EntityManagerProjector implements Event\Picker, QueryHandler
{
    use Query\Handling;

    public function __construct(Projector\Id $id, EntityManagerInterface $manager)
    {
        parent::__construct($id, $manager);
    }

    public function onUserRegistered(Events\UserRegistered $event) : void
    {
        $registered = $this->handleIsUserRegistered(new Queries\IsUserRegistered($event->email()));

        if ($registered) {
            return; // user already exists, we could write that uuid & email to some kind of reporting table with conflicts
        }

        $user = new RegisteredUser($event->userId(), $event->email(), $event->passwordHash(), $event->registeredAt());

        $this->manager->persist($user);
    }

    public function handleFindUser(Queries\FindUser $query) : ?RegisteredUser
    {
        $repository = $this->manager->getRepository(RegisteredUser::class);

        return $repository->findOneBy(['email' => $query->email()]);
    }

    public function handleIsUserRegistered(Queries\IsUserRegistered $query) : bool
    {
        return null !== $this->handleFindUser(new Queries\FindUser($query->email()));
    }

    public function pick(EventStore $store) : Event\Envelope
    {
        return $store->stream()->first(); // start listening from the first event in event store
    }
}
