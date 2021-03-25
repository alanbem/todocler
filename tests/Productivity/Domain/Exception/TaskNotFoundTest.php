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
use Productivity\Domain\Checklist;
use Productivity\Domain\Checklist\Task;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Exception\TaskNotFound
 */
class TaskNotFoundTest extends TestCase
{
    public function testException() : void
    {
        $listId = new Checklist\Id('list-1');
        $taskId = new Task\Id('task-1');

        $exception = new TaskNotFound($listId, $taskId);

        $this->assertSame('Task "task-1" not found.', $exception->getMessage());
        $this->assertSame($listId, $exception->listId());
        $this->assertSame($taskId, $exception->taskId());
    }
}
