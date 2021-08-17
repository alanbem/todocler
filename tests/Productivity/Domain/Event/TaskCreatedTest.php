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
 * @covers \Productivity\Domain\Event\TaskCreated
 */
final class TaskCreatedTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new TaskCreated('project-1', 'task-1', 'Task name', 'creator-1', $now = new \DateTimeImmutable());

        self::assertSame('project-1', $event->projectId());
        self::assertSame('task-1', $event->taskId());
        self::assertSame('Task name', $event->name());
        self::assertSame('creator-1', $event->creatorId());
        self::assertEquals($now, $event->createdAt());
        self::assertNotSame($now, $event->createdAt());
    }
}
