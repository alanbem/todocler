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
 * @covers \Productivity\Domain\Event\TaskRemoved
 */
final class TaskRemovedTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new TaskRemoved('project-1', 'task-1', 'user-1', $now = new \DateTimeImmutable());

        self::assertSame('project-1', $event->projectId());
        self::assertSame('task-1', $event->taskId());
        self::assertSame('user-1', $event->removerId());
        self::assertEquals($now, $event->removedAt());
        self::assertNotSame($now, $event->removedAt());
    }
}
