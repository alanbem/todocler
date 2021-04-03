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

use Productivity\Domain\Checklist\Task;
use Productivity\Domain\Command as Commands;
use Productivity\Domain\Event as Events;
use Productivity\Domain\Exception as Exceptions;
use Streak\Application\Command;
use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Event;
use Webmozart\Assert\Assert;

/**
 * `List` is a reserved word so I've used `Checklist` instead.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Command\CreateListTest
 * @see \Productivity\Domain\Command\RenameListTest
 * @see \Productivity\Domain\Command\RemoveListTest
 * @see \Productivity\Domain\Command\CreateTaskTest
 * @see \Productivity\Domain\Command\CompleteTaskTest
 * @see \Productivity\Domain\Command\RemoveTaskTest
 */
final class Checklist implements Event\Sourced\AggregateRoot, CommandHandler
{
    use Event\Sourced\AggregateRoot\Identification;
    use AggregateRoot\Comparison;
    use Event\Sourcing;
    use Command\Handling;

    private Clock $clock;

    private string $name;
    private string $creatorId;
    private \DateTimeImmutable $createdAt;
    private bool $removed = false;

    /**
     * @var Task[]
     */
    private array $tasks = [];

    public function __construct(Checklist\Id $id, Clock $clock)
    {
        $this->identifyBy($id);

        $this->clock = $clock;
    }

    public function listId() : string
    {
        return $this->aggregateRootId()->toString();
    }

    /**
     * @see Checklist::applyListCreated()
     */
    public function handleCreateList(Commands\CreateList $command) : void
    {
        Assert::notEmpty($command->creatorId(), 'User id is missing.');
        Assert::notEmpty($command->name(), 'Name is missing.');

        $this->apply(new Events\ListCreated($this->listId(), $command->name(), $command->creatorId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyListRenamed()
     */
    public function handleRenameList(Commands\RenameList $command) : void
    {
        Assert::notEmpty($command->editorId(), 'User id is missing.');
        Assert::notEmpty($command->name(), 'Name is missing.');

        if ($this->creatorId !== $command->editorId()) {
            throw new Exceptions\UserNotAllowed($command->editorId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ListNotFound($this->listId());
        }

        if ($this->name === $command->name()) {
            return; // nothing to change
        }

        $this->apply(new Events\ListRenamed($this->listId(), $command->name(), $command->editorId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyListRemoved()
     */
    public function handleRemoveList(Commands\RemoveList $command) : void
    {
        Assert::notEmpty($command->removerId(), 'User id is missing.');

        if ($this->creatorId !== $command->removerId()) {
            throw new Exceptions\UserNotAllowed($command->removerId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ListNotFound($this->listId());
        }

        $this->apply(new Events\ListRemoved($this->listId(), $command->removerId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyTaskCreated()
     */
    public function handleCreateTask(Commands\CreateTask $command) : void
    {
        Assert::notEmpty($command->creatorId(), 'User id is missing.');

        if ($this->creatorId !== $command->creatorId()) {
            throw new Exceptions\UserNotAllowed($command->creatorId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ListNotFound($this->listId());
        }

        $taskId = new Task\Id($command->taskId());

        if (null !== $this->findTask($taskId)) {
            throw new Exceptions\TaskAlreadyExists($this->listId(), $command->taskId());
        }

        Assert::notEmpty($command->name(), 'Name is missing.');

        $this->apply(new Events\TaskCreated($this->listId(), $command->taskId(), $command->name(), $command->creatorId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyTaskCompleted()
     */
    public function handleCompleteTask(Commands\CompleteTask $command) : void
    {
        Assert::notEmpty($command->userId(), 'User id is missing.');

        if ($this->creatorId !== $command->userId()) {
            throw new Exceptions\UserNotAllowed($command->userId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ListNotFound($this->listId());
        }

        $taskId = new Task\Id($command->taskId());
        $task = $this->findTask($taskId);

        if (null === $task) {
            throw new Exceptions\TaskNotFound($this->listId(), $command->taskId());
        }

        if (true === $task->completed()) {
            throw new Exceptions\TaskAlreadyCompleted($this->listId(), $command->taskId());
        }

        $this->apply(new Events\TaskCompleted($this->listId(), $command->taskId(), $command->userId(), $this->clock->now()));
    }

    /**
     * @see Checklist::applyTaskRemoved()
     */
    public function handleRemoveTask(Commands\RemoveTask $command) : void
    {
        Assert::notEmpty($command->removerId(), 'User id is missing.');

        if ($this->creatorId !== $command->removerId()) {
            throw new Exceptions\UserNotAllowed($command->removerId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ListNotFound($this->listId());
        }

        $taskId = new Task\Id($command->taskId());
        $task = $this->findTask($taskId);

        if (null === $task) {
            throw new Exceptions\TaskNotFound($this->listId(), $command->taskId());
        }

        $this->apply(new Events\TaskRemoved($this->listId(), $command->taskId(), $command->removerId(), $this->clock->now()));
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
        $this->name = $event->name();
        $this->creatorId = $event->creatorId();
        $this->createdAt = $event->createdAt();
    }

    /**
     * @see Checklist::handleRemoveList()
     */
    private function applyListRemoved(Events\ListRemoved $event) : void
    {
        $this->removed = true;
    }

    /**
     * @see Checklist::handleCreateList()
     */
    private function applyListRenamed(Events\ListRenamed $event) : void
    {
        $this->name = $event->name();
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

    /**
     * @see Checklist::handleRemoveTask()
     */
    private function applyTaskRemoved(Events\TaskRemoved $event) : void
    {
        $id = new Task\Id($event->taskId());

        foreach ($this->tasks as $position => $task) {
            if ($task->id()->equals($id)) {
                unset($this->tasks[$position]);
                break;
            }
        }
    }
}
