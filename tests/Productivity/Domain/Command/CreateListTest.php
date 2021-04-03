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

use Productivity\Application\Command\ListHandler;
use Productivity\Domain\Checklist;
use Productivity\Domain\Event\ListCreated;
use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Exception\AggregateAlreadyExists;
use Streak\Infrastructure\FixedClock;
use Streak\Infrastructure\Testing\AggregateRoot\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Command\ListHandler
 * @covers \Productivity\Domain\Command\CreateList
 * @covers \Productivity\Domain\Checklist
 * @covers \Productivity\Domain\Checklist\Task
 */
final class CreateListTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testCreatingList() : void
    {
        $this
            ->for(new Checklist\Id('list-1'))
            ->given()
            ->when(
                new CreateList('list-1', 'name', 'user-1'),
            )
            ->then(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testCreatingListThatExistsAlready() : void
    {
        $this->expectException(AggregateAlreadyExists::class);
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new CreateList('list-1', 'name', 'user-1'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new CreateList('list-1', 'name', 'user-1');

        self::assertSame('list-1', $command->listId());
        self::assertSame('name', $command->name());
        self::assertSame('user-1', $command->creatorId());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Checklist\Factory($this->clock);
    }

    protected function createHandler(AggregateRoot\Factory $factory, AggregateRoot\Repository $repository) : CommandHandler
    {
        return new ListHandler($factory, $repository);
    }
}
