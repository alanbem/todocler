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
use Productivity\Domain\Exception\ListNotFound;
use Productivity\Domain\Exception\UserNotAllowed;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Infrastructure\FixedClock;
use Streak\Infrastructure\Testing\AggregateRoot\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Command\RemoveList
 * @covers \Productivity\Domain\Checklist
 * @covers \Productivity\Domain\Checklist\Task
 */
final class RemoveListTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = new FixedClock(new \DateTimeImmutable('2021-03-25 17:49:00'));
    }

    public function testRemovingList() : void
    {
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RemoveList('list-1', 'user-1'),
            )
            ->then(
                new ListRemoved('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            );
    }

    public function testRemovingListWithWrongUser() : void
    {
        $this->expectExceptionObject(new UserNotAllowed('user-2'));
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RemoveList('list-1', 'user-2'),
            )
            ->then();
    }

    public function testRemovingListOnRemovedList() : void
    {
        $this->expectExceptionObject(new ListNotFound('list-1'));
        $this
            ->for(new Checklist\Id('list-1'))
            ->given(
                new ListCreated('list-1', 'name', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
                new ListRemoved('list-1', 'user-1', new \DateTimeImmutable('2021-03-25 17:49:00')),
            )
            ->when(
                new RemoveList('list-1', 'user-1'),
            )
            ->then();
    }

    public function testCommand() : void
    {
        $command = new RemoveList('list-1', 'user-1');

        self::assertSame('list-1', $command->listId());
        self::assertSame('user-1', $command->removerId());
    }

    protected function createFactory() : AggregateRoot\Factory
    {
        return new Checklist\Factory($this->clock);
    }
}
