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

namespace Productivity\Domain\Command;

use Productivity\Domain\Checklist;
use Productivity\Domain\Event\ListCreated;
use Productivity\Domain\Event\ListRemoved;
use Productivity\Domain\Event\TaskCreated;
use Productivity\Domain\Exception\ListNotFound;
use Productivity\Domain\Exception\TaskAlreadyExists;
use Productivity\Domain\Exception\UserNotAllowed;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Infrastructure\Domain\Clock\FixedClock;
use Streak\Infrastructure\Domain\Testing\AggregateRoot\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Command\CreateTask
 * @covers \Productivity\Domain\Checklist
 * @covers \Productivity\Domain\Checklist\Task
 */
final class CreateTaskTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testCreatingTask() : void
    {
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CreateTask('list-1', 'task-1', 'My first task', 'user-1'),
            )
            ->then(
                new TaskCreated('list-1', 'task-1', 'My first task', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testCreatingAlreadyExistingTask() : void
    {
        $this->expectExceptionObject(new TaskAlreadyExists('list-1', 'task-1'));

        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new TaskCreated('list-1', 'task-1', 'My first task', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CreateTask('list-1', 'task-1', 'My first task', 'user-1'),
            )
            ->then();
    }

    public function testCreatingTaskByWrongUser() : void
    {
        $this->expectExceptionObject(new UserNotAllowed('user-2'));

        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CreateTask('list-1', 'task-1', 'My first task', 'user-2'),
            )
            ->then();
    }

    public function testCreatingTaskOnRemovedList() : void
    {
        $this->expectExceptionObject(new ListNotFound('list-1'));

        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new ListRemoved('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CreateTask('list-1', 'task-1', 'My first task', 'user-1'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new CreateTask('list-1', 'task-1', 'My first task', 'user-1');

        self::assertSame('list-1', $command->listId());
        self::assertSame('task-1', $command->taskId());
        self::assertSame('user-1', $command->creatorId());
        self::assertSame('My first task', $command->name());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Checklist\Factory($this->clock);
    }
}
