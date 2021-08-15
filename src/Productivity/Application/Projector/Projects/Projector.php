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

namespace Productivity\Application\Projector\Projects;

use Doctrine\ORM\EntityManagerInterface;
use Productivity\Application\Projector\Projects;
use Productivity\Application\Projector\Projects\Doctrine\Entity;
use Productivity\Application\Query as Queries;
use Productivity\Domain\Event as Events;
use Shared\Application\Projector\Doctrine;
use Streak\Domain\Event;
use Streak\Domain\EventStore;
use Streak\Domain\Query;
use Streak\Domain\QueryHandler;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Projector\Projects\ProjectorTest
 * @see \Productivity\Application\Projector\Projects\Projector\Factory
 */
final class Projector extends Doctrine\EntityManagerProjector implements Event\Picker, QueryHandler
{
    use Query\Handling;

    public function __construct(Projects\Projector\Id $id, EntityManagerInterface $manager)
    {
        parent::__construct($id, $manager);
    }

    public function onListCreated(Events\ProjectCreated $event) : void
    {
        $project = new Entity\Project($event->projectId(), $event->name(), $event->creatorId(), $event->createdAt(), $event->createdAt());

        $this->manager->persist($project);
    }

    public function onListRenamed(Events\ProjectRenamed $event) : void
    {
        /** @var Entity\Project $project */
        $project = $this->manager->getRepository(Entity\Project::class)->find($event->projectId());
        $project->rename($event->name(), $event->modifiedAt());
    }

    public function onListRemoved(Events\ProjectRemoved $event) : void
    {
        /** @var Entity\Project $project */
        $project = $this->manager->getRepository(Entity\Project::class)->find($event->projectId());

        $this->manager->remove($project);
    }

    public function onTaskCreated(Events\TaskCreated $event) : void
    {
        /** @var Entity\Project $project */
        $project = $this->manager->getRepository(Entity\Project::class)->find($event->projectId());
        $project->addTask($event->taskId(), $event->name(), $event->creatorId(), $event->createdAt(), $event->createdAt());
    }

    public function onTaskCompleted(Events\TaskCompleted $event) : void
    {
        /** @var Entity\Task $task */
        $task = $this->manager->getRepository(Entity\Task::class)->find(['project' => $event->projectId(), 'id' => $event->taskId()]);
        $task->complete($event->completedAt());
    }

    public function onTaskRemoved(Events\TaskRemoved $event) : void
    {
        /** @var Entity\Task $project */
        $task = $this->manager->getRepository(Entity\Task::class)->find(['project' => $event->projectId(), 'id' => $event->taskId()]);

        $this->manager->remove($task);
    }

    /**
     * @return Entity\Project[]
     */
    public function handleBrowseProjects(Queries\BrowseProjects $query) : iterable
    {
        $criteria = [];

        if (null !== $query->ownerId()) {
            $criteria['userId'] = $query->ownerId();
        }

        /** @var Entity\Project[] $projects */
        $projects = $this->manager->getRepository(Entity\Project::class)->findBy($criteria);

        return $projects;
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
