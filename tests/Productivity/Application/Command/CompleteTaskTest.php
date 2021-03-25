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

namespace Productivity\Application\Command;

use Productivity\Domain\Checklist;
use Productivity\Domain\Event\ListCreated;
use Productivity\Domain\Event\TaskCompleted;
use Productivity\Domain\Event\TaskCreated;
use Productivity\Domain\Exception\TaskAlreadyCompleted;
use Productivity\Domain\Exception\TaskNotFound;
use Productivity\Domain\Exception\UserNotPermitted;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Infrastructure\FixedClock;
use Streak\Infrastructure\Testing\AggregateRoot\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Command\CompleteTask
 * @covers \Productivity\Domain\Checklist
 * @covers \Productivity\Domain\Checklist\Task
 */
class CompleteTaskTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testCompletingTask() : void
    {
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new TaskCreated('list-1', 'task-1', 'My first task', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CompleteTask('list-1', 'task-1', 'user-1'),
            )
            ->then(
                new TaskCompleted('list-1', 'task-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testCompletingAlreadyCompletedTask() : void
    {
        $exception = new TaskAlreadyCompleted(new Checklist\Id('list-1'), new Checklist\Task\Id('task-1'));

        $this->expectExceptionObject($exception);
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new TaskCreated('list-1', 'task-1', 'My first task', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new TaskCompleted('list-1', 'task-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CompleteTask('list-1', 'task-1', 'user-1'),
            )
            ->then();
    }

    public function testCompletingNonExistentTask() : void
    {
        $exception = new TaskNotFound(new Checklist\Id('list-1'), new Checklist\Task\Id('task-1'));

        $this->expectExceptionObject($exception);
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CompleteTask('list-1', 'task-1', 'user-1'),
            )
            ->then();
    }

    public function testCompletingTaskByWrongUser() : void
    {
        $exception = new UserNotPermitted('user-2');

        $this->expectExceptionObject($exception);
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new TaskCreated('list-1', 'task-1', 'My first task', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CompleteTask('list-1', 'task-1', 'user-2'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new CompleteTask('list-1', 'task-1', 'user-1');

        $this->assertSame('list-1', $command->listId());
        $this->assertSame('task-1', $command->taskId());
        $this->assertSame('user-1', $command->userId());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Checklist\Factory($this->clock);
    }
}
