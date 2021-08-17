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

use Productivity\Domain\Command as Commands;
use Productivity\Domain\Event as Events;
use Productivity\Domain\Exception as Exceptions;
use Productivity\Domain\Project\Task;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Command;
use Streak\Domain\CommandHandler;
use Streak\Domain\Event;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Project\Factory
 * @see \Productivity\Domain\Command\CreateProjectTest
 * @see \Productivity\Domain\Command\RenameProjectTest
 * @see \Productivity\Domain\Command\RemoveProjectTest
 * @see \Productivity\Domain\Command\CreateTaskTest
 * @see \Productivity\Domain\Command\CompleteTaskTest
 * @see \Productivity\Domain\Command\RemoveTaskTest
 */
final class Project implements Event\Sourced\AggregateRoot, CommandHandler
{
    use AggregateRoot\Comparison;
    use Command\Handling;
    use Event\Sourced\AggregateRoot\Identification;
    use Event\Sourcing;

    private Clock $clock;

    private string $name;
    private string $creatorId;
    private \DateTimeImmutable $createdAt;
    private bool $removed = false;

    /**
     * @var Task[]
     */
    private array $tasks = [];

    public function __construct(Project\Id $id, Clock $clock)
    {
        $this->identifyBy($id);

        $this->clock = $clock;
    }

    public function projectId() : string
    {
        return $this->aggregateRootId()->toString();
    }

    /**
     * @see Project::applyProjectCreated()
     */
    public function handleCreateProject(Commands\CreateProject $command) : void
    {
        Assert::notEmpty($command->creatorId(), 'User id is missing.');
        Assert::notEmpty($command->name(), 'Name is missing.');

        $this->apply(new Events\ProjectCreated($this->projectId(), $command->name(), $command->creatorId(), $this->clock->now()));
    }

    /**
     * @see Project::applyProjectRenamed()
     */
    public function handleRenameProject(Commands\RenameProject $command) : void
    {
        Assert::notEmpty($command->editorId(), 'User id is missing.');
        Assert::notEmpty($command->name(), 'Name is missing.');

        if ($this->creatorId !== $command->editorId()) {
            throw new Exceptions\UserNotAllowed($command->editorId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ProjectNotFound($this->projectId());
        }

        if ($this->name === $command->name()) {
            return; // nothing to change
        }

        $this->apply(new Events\ProjectRenamed($this->projectId(), $command->name(), $command->editorId(), $this->clock->now()));
    }

    /**
     * @see Project::applyProjectRemoved()
     */
    public function handleRemoveProject(Commands\RemoveProject $command) : void
    {
        Assert::notEmpty($command->removerId(), 'User id is missing.');

        if ($this->creatorId !== $command->removerId()) {
            throw new Exceptions\UserNotAllowed($command->removerId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ProjectNotFound($this->projectId());
        }

        $this->apply(new Events\ProjectRemoved($this->projectId(), $command->removerId(), $this->clock->now()));
    }

    /**
     * @see Project::applyTaskCreated()
     */
    public function handleCreateTask(Commands\CreateTask $command) : void
    {
        Assert::notEmpty($command->creatorId(), 'User id is missing.');

        if ($this->creatorId !== $command->creatorId()) {
            throw new Exceptions\UserNotAllowed($command->creatorId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ProjectNotFound($this->projectId());
        }

        $taskId = new Task\Id($command->taskId());

        if (null !== $this->findTask($taskId)) {
            throw new Exceptions\TaskAlreadyExists($this->projectId(), $command->taskId());
        }

        Assert::notEmpty($command->name(), 'Name is missing.');

        $this->apply(new Events\TaskCreated($this->projectId(), $command->taskId(), $command->name(), $command->creatorId(), $this->clock->now()));
    }

    /**
     * @see Project::applyTaskCompleted()
     */
    public function handleCompleteTask(Commands\CompleteTask $command) : void
    {
        Assert::notEmpty($command->userId(), 'User id is missing.');

        if ($this->creatorId !== $command->userId()) {
            throw new Exceptions\UserNotAllowed($command->userId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ProjectNotFound($this->projectId());
        }

        $taskId = new Task\Id($command->taskId());
        $task = $this->findTask($taskId);

        if (null === $task) {
            throw new Exceptions\TaskNotFound($this->projectId(), $command->taskId());
        }

        if (true === $task->completed()) {
            throw new Exceptions\TaskAlreadyCompleted($this->projectId(), $command->taskId());
        }

        $this->apply(new Events\TaskCompleted($this->projectId(), $command->taskId(), $command->userId(), $this->clock->now()));
    }

    /**
     * @see Project::applyTaskRemoved()
     */
    public function handleRemoveTask(Commands\RemoveTask $command) : void
    {
        Assert::notEmpty($command->removerId(), 'User id is missing.');

        if ($this->creatorId !== $command->removerId()) {
            throw new Exceptions\UserNotAllowed($command->removerId());
        }

        if (true === $this->removed) {
            throw new Exceptions\ProjectNotFound($this->projectId());
        }

        $taskId = new Task\Id($command->taskId());
        $task = $this->findTask($taskId);

        if (null === $task) {
            throw new Exceptions\TaskNotFound($this->projectId(), $command->taskId());
        }

        $this->apply(new Events\TaskRemoved($this->projectId(), $command->taskId(), $command->removerId(), $this->clock->now()));
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
     * @see Project::handleCreateProject()
     */
    private function applyProjectCreated(Events\ProjectCreated $event) : void
    {
        $this->name = $event->name();
        $this->creatorId = $event->creatorId();
        $this->createdAt = $event->createdAt();
    }

    /**
     * @see Project::handleRemoveProject()
     */
    private function applyProjectRemoved(Events\ProjectRemoved $event) : void
    {
        $this->removed = true;
    }

    /**
     * @see Project::handleCreateProject()
     */
    private function applyProjectRenamed(Events\ProjectRenamed $event) : void
    {
        $this->name = $event->name();
    }

    /**
     * @see Project::handleCreateTask()
     */
    private function applyTaskCreated(Events\TaskCreated $event) : void
    {
        $this->tasks[] = new Task(new Task\Id($event->taskId()), $event->name(), $event->createdAt());
    }

    /**
     * @see Project::handleCompleteTask()
     */
    private function applyTaskCompleted(Events\TaskCompleted $event) : void
    {
        $task = $this->findTask(new Task\Id($event->taskId()));
        $task->complete();
    }

    /**
     * @see Project::handleRemoveTask()
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
