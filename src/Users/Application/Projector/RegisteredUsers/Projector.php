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
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Streak\Application\Query;
use Streak\Application\QueryHandler;
use Streak\Domain\Event;
use Streak\Domain\EventStore;
use Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser;
use Users\Application\Query as Queries;
use Users\Domain\Event as Events;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Projector\RegisteredUsers\ProjectorTest
 */
class Projector implements Event\Listener, Event\Listener\Resettable, Event\Picker, Event\Filterer, QueryHandler
{
    use Event\Listener\Identifying;
    use Event\Listener\Listening;
    use Event\Listener\Filtering;
    use Query\Handling;

    private EntityManagerInterface $manager;

    public function __construct(Projector\Id $id, EntityManagerInterface $manager)
    {
        $this->identifyBy($id);

        $this->manager = $manager;
    }

    public function onUserRegistered(Events\UserRegistered $event) : void
    {
        $user = $this->handleFindUser(new Queries\FindUser($event->email()));

        if ($user) {
            return; // user already exists, we could write that uuid & email to some kind of reporting table with conflicts
        }

        $user = new RegisteredUser($event->userId(), $event->email(), $event->passwordHash(), $event->salt(), $event->registeredAt());

        $this->manager->persist($user);
    }

    public function handleFindUser(Queries\FindUser $query) : ?RegisteredUser
    {
        $repository = $this->manager->getRepository(RegisteredUser::class);

        return $repository->findOneBy(['username' => $query->email()]);
    }

    public function reset() : void
    {
        $this->manager->beginTransaction();

        $tool = new SchemaTool($this->manager);
        $meta = $this->manager->getMetadataFactory()->getAllMetadata();

        try {
            $tool->dropSchema($meta);
            $tool->createSchema($meta);
            // @codeCoverageIgnoreStart
        } catch (ToolsException $e) {
            $this->manager->rollback();

            throw $e;
        }
        // @codeCoverageIgnoreStop

        $this->manager->commit();
    }

    public function pick(EventStore $store) : Event\Envelope
    {
        return $store->stream()->first(); // start listening from the first event in event store
    }

    protected function preEvent(Event $event) : void
    {
        $this->manager->clear();
        $this->manager->beginTransaction();
    }

    protected function postEvent(Event $event) : void
    {
        $this->manager->flush();
        $this->manager->commit();
    }

    protected function onException(\Throwable $exception) : void
    {
        $this->manager->rollBack();
    }
}
