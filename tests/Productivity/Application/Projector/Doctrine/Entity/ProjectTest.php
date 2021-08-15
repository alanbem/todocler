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
 * @covers \Productivity\Application\Projector\Projects\Doctrine\Entity\Project
 */
final class ProjectTest extends TestCase
{
    public function testList() : void
    {
        $project = new Project('f45a75db-670c-4116-bf97-b8cd07eb09d0', 'Project #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated = new \DateTimeImmutable(), $created = new \DateTimeImmutable());

        self::assertSame('f45a75db-670c-4116-bf97-b8cd07eb09d0', $project->getId());
        self::assertSame('Project #1', $project->getName());
        self::assertSame('4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $project->getUserId());
        self::assertSame($updated, $project->getUpdatedAt());
        self::assertSame($created, $project->getCreatedAt());

        self::assertEmpty($project->getTasks());

        $project->addTask('1d3db856-d874-4bc5-a41e-4268007bf6cd', 'Task #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated = new \DateTimeImmutable(), $created = new \DateTimeImmutable());

        self::assertCount(1, $project->getTasks());
        self::assertEquals(new Task($project, '1d3db856-d874-4bc5-a41e-4268007bf6cd', 'Task #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated, $created), $project->getTasks()[0]);
    }
}
