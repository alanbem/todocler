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

namespace Productivity\Domain;

use Productivity\Application\Command as Commands;
use Productivity\Domain\Checklist\Task;
use Productivity\Domain\Event as Events;
use Productivity\Domain\Exception as Exceptions;
use Streak\Application\Command;
use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Event;

/**
 * `List` is a reserved word so I've used `Checklist` instead.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Checklist implements Event\Sourced\AggregateRoot, CommandHandler
{
    use Event\Sourced\AggregateRoot\Identification;
    use AggregateRoot\Comparison;
    use Event\Sourcing;
    use Command\Handling;

    private Clock $clock;

    private ?string $creatorId = null;
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Task[]
     */
    private array $tasks = [];

    public function __construct(Checklist\Id $id, Clock $clock)
    {
        $this->identifyBy($id);

        $this->clock = $clock;
    }

    public function listId() : Checklist\Id
    {
        return $this->aggregateRootId();
    }

    /**
     * @see Checklist::applyListCreated()
     */
    public function handleCreateList(Commands\CreateList $command) : void
    {
        $this->apply(new Events\ListCreated($this->listId()->toString(), $command->creatorId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyTaskCreated()
     */
    public function handleCreateTask(Commands\CreateTask $command) : void
    {
        if ($this->creatorId !== $command->creatorId()) {
            throw new Exceptions\UserNotPermitted($command->creatorId());
        }

        $taskId = new Task\Id($command->taskId());

        if (null !== $this->findTask($taskId)) {
            throw new Exceptions\TaskAlreadyExists($this->listId(), $taskId);
        }

        $this->apply(new Events\TaskCreated($this->listId()->toString(), $command->taskId(), $command->name(), $command->creatorId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyTaskCompleted()
     */
    public function handleCompleteTask(Commands\CompleteTask $command) : void
    {
        if ($this->creatorId !== $command->userId()) {
            throw new Exceptions\UserNotPermitted($command->userId());
        }

        $taskId = new Task\Id($command->taskId());
        $task = $this->findTask($taskId);

        if (null === $task) {
            throw new Exceptions\TaskNotFound($this->listId(), $taskId);
        }

        if (true === $task->completed()) {
            throw new Exceptions\TaskAlreadyCompleted($this->listId(), $taskId);
        }

        $this->apply(new Events\TaskCompleted($this->listId()->toString(), $command->taskId(), $command->userId(), $this->clock->now()));
    }

    private function findTask(Task\Id $id) : ?Task
    {
        foreach ($this->tasks as $task) {
            if ($task->id()->equals($id)) {
                return $task;
            }
        }

        return null;
    }

    /**
     * @see Checklist::handleCreateList()
     */
    private function applyListCreated(Events\ListCreated $event) : void
    {
        $this->creatorId = $event->creatorId();
        $this->createdAt = $event->createdAt();
    }

    /**
     * @see Checklist::handleCreateTask()
     */
    private function applyTaskCreated(Events\TaskCreated $event) : void
    {
        $this->tasks[] = new Task(new Task\Id($event->taskId()), $event->name(), $event->createdAt());
    }

    /**
     * @see Checklist::handleCompleteTask()
     */
    private function applyTaskCompleted(Events\TaskCompleted $event) : void
    {
        $task = $this->findTask(new Task\Id($event->taskId()));
        $task->complete();
    }
}
