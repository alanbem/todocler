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

namespace Productivity\Domain\Exception;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Exception\TaskNotFound
 */
final class TaskNotFoundTest extends TestCase
{
    public function testException() : void
    {
        $exception = new TaskNotFound('project-1', 'task-1');

        self::assertSame('Task "task-1" not found.', $exception->getMessage());
        self::assertSame('project-1', $exception->projectId());
        self::assertSame('task-1', $exception->taskId());
    }
}
