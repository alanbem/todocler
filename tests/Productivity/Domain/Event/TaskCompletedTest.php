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

namespace Productivity\Domain\Event;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Event\TaskCompleted
 */
final class TaskCompletedTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new TaskCompleted('list-1', 'task-1', 'user-1', $now = new \DateTimeImmutable());

        self::assertSame('list-1', $event->listId());
        self::assertSame('task-1', $event->taskId());
        self::assertSame('user-1', $event->userId());
        self::assertEquals($now, $event->completedAt());
        self::assertNotSame($now, $event->completedAt());
    }
}
