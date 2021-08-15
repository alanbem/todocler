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
 * @covers \Productivity\Domain\Event\ProjectRenamed
 */
final class ProjectRenamedTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new ProjectRenamed('project-1', 'name', 'editor-1', $now = new \DateTimeImmutable());

        self::assertSame('project-1', $event->projectId());
        self::assertSame('name', $event->name());
        self::assertSame('editor-1', $event->editorId());
        self::assertEquals($now, $event->modifiedAt());
        self::assertNotSame($now, $event->modifiedAt());
    }
}
