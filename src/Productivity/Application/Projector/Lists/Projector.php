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

namespace Productivity\Application\Projector\Lists;

use Doctrine\ORM\EntityManagerInterface;
use Productivity\Application\Projector\Lists;
use Productivity\Application\Projector\Lists\Doctrine\Entity;
use Productivity\Application\Query as Queries;
use Productivity\Domain\Event as Events;
use Shared\Application\Projector\Doctrine;
use Streak\Application\Query;
use Streak\Application\QueryHandler;
use Streak\Domain\Event;
use Streak\Domain\EventStore;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Projector\Lists\ProjectorTest
 */
class Projector extends Doctrine\EntityManagerProjector implements Event\Picker, QueryHandler
{
    use Query\Handling;

    public function __construct(Lists\Projector\Id $id, EntityManagerInterface $manager)
    {
        parent::__construct($id, $manager);
    }

    public function onListCreated(Events\ListCreated $event) : void
    {
        $list = new Entity\Checklist($event->listId(), $event->name(), $event->creatorId(), $event->createdAt(), $event->createdAt());

        $this->manager->persist($list);
    }

    public function onListRenamed(Events\ListRenamed $event) : void
    {
        /** @var Entity\Checklist $list */
        $list = $this->manager->getRepository(Entity\Checklist::class)->find($event->listId());
        $list->rename($event->name(), $event->modifiedAt());
    }

    public function onListRemoved(Events\ListRemoved $event) : void
    {
        /** @var Entity\Checklist $list */
        $list = $this->manager->getRepository(Entity\Checklist::class)->find($event->listId());

        $this->manager->remove($list);
    }

    public function onTaskCreated(Events\TaskCreated $event) : void
    {
        /** @var Entity\Checklist $list */
        $list = $this->manager->getRepository(Entity\Checklist::class)->find($event->listId());
        $list->addTask($event->taskId(), $event->name(), $event->creatorId(), $event->createdAt(), $event->createdAt());
    }

    public function onTaskCompleted(Events\TaskCompleted $event) : void
    {
        /** @var Entity\Task $list */
        $task = $this->manager->getRepository(Entity\Task::class)->find($event->taskId());
        $task->complete($event->completedAt());
    }

    public function onTaskRemoved(Events\TaskRemoved $event) : void
    {
        /** @var Entity\Task $list */
        $task = $this->manager->getRepository(Entity\Task::class)->find($event->taskId());

        $this->manager->remove($task);
    }

    /**
     * @return Entity\Checklist[]
     */
    public function handleBrowseChecklists(Queries\BrowseChecklists $query) : iterable
    {
        $criteria = [];

        if (null !== $query->ownerId()) {
            $criteria['userId'] = $query->ownerId();
        }

        /** @var Entity\Checklist[] $lists */
        $lists = $this->manager->getRepository(Entity\Checklist::class)->findBy($criteria);

        return $lists;
    }

    /**
     * @return Entity\Task[]
     */
    public function handleBrowseTasks(Queries\BrowseTasks $query) : iterable
    {
        $criteria = [];

        if (null !== $query->ownerId()) {
            $criteria['userId'] = $query->ownerId();
        }

        /** @var Entity\Task[] $tasks */
        $tasks = $this->manager->getRepository(Entity\Task::class)->findBy($criteria);

        return $tasks;
    }

    public function pick(EventStore $store) : Event\Envelope
    {
        return $store->stream()->first(); // start listening from the first event in event store
    }
}
