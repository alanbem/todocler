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
 * @covers \Productivity\Domain\Event\ListCreated
 */
final class ListCreatedTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new ListCreated('list-1', 'name', 'creator-1', $now = new \DateTimeImmutable());

        $this->assertSame('list-1', $event->listId());
        $this->assertSame('name', $event->name());
        $this->assertSame('creator-1', $event->creatorId());
        $this->assertEquals($now, $event->createdAt());
        $this->assertNotSame($now, $event->createdAt());
    }
}
