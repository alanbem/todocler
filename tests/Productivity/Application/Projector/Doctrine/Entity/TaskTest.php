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

namespace Productivity\Application\Projector\Doctrine\Entity;

use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Projects\Doctrine\Entity\Project;
use Productivity\Application\Projector\Projects\Doctrine\Entity\Task;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Projects\Doctrine\Entity\Task
 */
final class TaskTest extends TestCase
{
    public function testTask() : void
    {
        $project = new Project('f45a75db-670c-4116-bf97-b8cd07eb09d0', 'Project #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', new \DateTimeImmutable(), new \DateTimeImmutable());
        $task = new Task($project, '1d3db856-d874-4bc5-a41e-4268007bf6cd', 'Task #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated = new \DateTimeImmutable(), $created = new \DateTimeImmutable());

        self::assertSame($project, $task->getProject());
        self::assertSame('1d3db856-d874-4bc5-a41e-4268007bf6cd', $task->getId());
        self::assertSame('Task #1', $task->getName());
        self::assertSame('4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $task->getUserId());
        self::assertSame($updated, $task->getUpdatedAt());
        self::assertSame($created, $task->getCreatedAt());

        self::assertFalse($task->isCompleted());

        $task->complete($updated = new \DateTimeImmutable());

        self::assertTrue($task->isCompleted());
        self::assertSame($updated, $task->getUpdatedAt());
    }
}
