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

namespace Productivity\Domain\Project;

use Productivity\Domain\Event as Events;
use Productivity\Domain\Exception;
use Productivity\Domain\Project;
use Streak\Domain\Clock;
use Streak\Domain\Entity;
use Streak\Domain\Event;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
final class Task implements Event\Sourced\Entity
{
    use Entity\Comparison;
    use Entity\EventSourcing;
    use Entity\Identification;

    private \DateTimeImmutable $createdAt;
    private bool $completed = false;

    public function __construct(Project $project, Task\Id $id, private string $name, private Clock $clock)
    {
        $this->identifyBy($id);
        $this->registerAggregateRoot($project);

        $this->createdAt = $this->clock->now();
    }

    public function projectId() : string
    {
        return $this->aggregateRoot()->id()->toString();
    }

    public function taskId() : string
    {
        return $this->id()->toString();
    }

    /**
     * @see Task::applyTaskCompleted()
     */
    public function complete(string $userId) : void
    {
        if (true === $this->completed) {
            throw new Exception\TaskAlreadyCompleted($this->projectId(), $this->taskId());
        }

        $this->apply(new Events\TaskCompleted($this->projectId(), $this->taskId(), $userId, $this->clock->now()));
    }

    /**
     * @see Task::applyTaskCompleted()
     */
    public function remove(string $userId) : void
    {
        $this->apply(new Events\TaskRemoved($this->projectId(), $this->taskId(), $userId, $this->clock->now()));
    }

    /**
     * @see Task::complete()
     */
    private function applyTaskCompleted(Events\TaskCompleted $event) : void
    {
        $this->completed = true;
    }

    /**
     * @see Task::remove()
     */
    private function applyTaskRemoved(Events\TaskRemoved $event) : void
    {
    }
}
