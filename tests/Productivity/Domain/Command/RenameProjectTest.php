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
use Productivity\Domain\Event\ProjectRenamed;
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
 * @covers \Productivity\Domain\Command\RenameProject
 * @covers \Productivity\Domain\Project
 * @covers \Productivity\Domain\Project\Task
 */
final class RenameProjectTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testRenamingList() : void
    {
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RenameProject('project-1', 'new name', 'user-1'),
            )
            ->then(
                new ProjectRenamed('project-1', 'new name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testRenamingListWithTheSameName() : void
    {
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RenameProject('project-1', 'name', 'user-1'),
            )
            ->then();
    }

    public function testRenamingListWithWrongUser() : void
    {
        $this->expectExceptionObject(new UserNotAllowed('user-2'));
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RenameProject('project-1', 'new name', 'user-2'),
            )
            ->then();
    }

    public function testRenamingListOnRemovedList() : void
    {
        $this->expectExceptionObject(new ProjectNotFound('project-1'));
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new ProjectRemoved('project-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RenameProject('project-1', 'name', 'user-1'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new RenameProject('project-1', 'name', 'user-1');

        self::assertSame('project-1', $command->projectId());
        self::assertSame('name', $command->name());
        self::assertSame('user-1', $command->editorId());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Project\Factory($this->clock);
    }
}
