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

use Productivity\Application\Command\ProjectHandler;
use Productivity\Domain\Event\ProjectCreated;
use Productivity\Domain\Project;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\CommandHandler;
use Streak\Domain\Exception\AggregateAlreadyExists;
use Streak\Infrastructure\Domain\Clock\FixedClock;
use Streak\Infrastructure\Domain\Testing\AggregateRoot\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Command\ProjectHandler
 * @covers \Productivity\Domain\Command\CreateProject
 * @covers \Productivity\Domain\Project
 * @covers \Productivity\Domain\Project\Task
 */
final class CreateProjectTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testCreatingList() : void
    {
        $this
            ->for(new Project\Id('project-1'))
            ->given()
            ->when(
                new CreateProject('project-1', 'name', 'user-1'),
            )
            ->then(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testCreatingListThatExistsAlready() : void
    {
        $this->expectException(AggregateAlreadyExists::class);
        $this
            ->for(new Project\Id('project-1'))
            ->given(
                new ProjectCreated('project-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CreateProject('project-1', 'name', 'user-1'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new CreateProject('project-1', 'name', 'user-1');

        self::assertSame('project-1', $command->projectId());
        self::assertSame('name', $command->name());
        self::assertSame('user-1', $command->creatorId());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Project\Factory($this->clock);
    }

    protected function createHandler(AggregateRoot\Factory $factory, AggregateRoot\Repository $repository) : CommandHandler
    {
        return new ProjectHandler($factory, $repository);
    }
}
