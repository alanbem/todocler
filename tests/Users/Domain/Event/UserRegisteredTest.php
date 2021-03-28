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

namespace Users\Domain\Event;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Domain\Event\UserRegistered
 */
class UserRegisteredTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new UserRegistered('user-1', 'alan.bem@example.com', 'hash', 'salt', $now = new \DateTimeImmutable());

        $this->assertSame('user-1', $event->userId());
        $this->assertSame('alan.bem@example.com', $event->email());
        $this->assertSame('hash', $event->passwordHash());
        $this->assertSame('salt', $event->salt());
        $this->assertEquals($now, $event->registeredAt());
        $this->assertNotSame($now, $event->registeredAt());
    }
}
