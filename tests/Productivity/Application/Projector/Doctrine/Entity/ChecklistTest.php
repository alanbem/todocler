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
use Productivity\Application\Projector\Lists\Doctrine\Entity\Checklist;
use Productivity\Application\Projector\Lists\Doctrine\Entity\Task;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Lists\Doctrine\Entity\Checklist
 */
class ChecklistTest extends TestCase
{
    public function testList()
    {
        $list = new Checklist('f45a75db-670c-4116-bf97-b8cd07eb09d0', 'List #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated = new \DateTimeImmutable(), $created = new \DateTimeImmutable());

        $this->assertSame('f45a75db-670c-4116-bf97-b8cd07eb09d0', $list->id());
        $this->assertSame('List #1', $list->name());
        $this->assertSame('4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $list->userId());
        $this->assertSame($updated, $list->updatedAt());
        $this->assertSame($created, $list->createdAt());

        $this->assertEmpty($list->tasks());

        $list->addTask('1d3db856-d874-4bc5-a41e-4268007bf6cd', 'Task #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated = new \DateTimeImmutable(), $created = new \DateTimeImmutable());

        $this->assertCount(1, $list->tasks());
        $this->assertEquals(new Task($list, '1d3db856-d874-4bc5-a41e-4268007bf6cd', 'Task #1', '4cf1d6b1-4437-46a2-881f-3d6bc39b716c', $updated, $created), $list->tasks()[0]);
    }
}
