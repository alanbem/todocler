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
 * @covers \Productivity\Domain\Event\ProjectCreated
 */
final class ProjectCreatedTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new ProjectCreated('project-1', 'name', 'creator-1', $now = new \DateTimeImmutable());

        self::assertSame('project-1', $event->projectId());
        self::assertSame('name', $event->name());
        self::assertSame('creator-1', $event->creatorId());
        self::assertEquals($now, $event->createdAt());
        self::assertNotSame($now, $event->createdAt());
    }
}
