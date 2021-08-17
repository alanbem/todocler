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

use Productivity\Domain\Event\ProjectCreated;
use Productivity\Domain\Event\ProjectRemoved;
use Productivity\Domain\Exception\ProjectNotFound;
use Productivity\Domain\Exception\UserNotAllowed;
use Productivity\Domain\Project;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Infrastructure\Domain\Clock\FixedClock;
use Streak\Infrastructure\Domain\Testing\AggregateRoot\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Command\RemoveProject
 * @covers \Productivity\Domain\Project
 * @covers \Productivity\Domain\Project\Task
 */
final class RemoveProjectTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testRemovingList() : void
    {
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RemoveProject('project-1', 'user-1'),
            )
            ->then(
                new ProjectRemoved('project-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testRemovingListWithWrongUser() : void
    {
        $this->expectExceptionObject(new UserNotAllowed('user-2'));
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RemoveProject('project-1', 'user-2'),
            )
            ->then();
    }

    public function testRemovingListOnRemovedList() : void
    {
        $this->expectExceptionObject(new ProjectNotFound('project-1'));
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new ProjectRemoved('project-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RemoveProject('project-1', 'user-1'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new RemoveProject('project-1', 'user-1');

        self::assertSame('project-1', $command->projectId());
        self::assertSame('user-1', $command->removerId());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Project\Factory($this->clock);
    }
}
