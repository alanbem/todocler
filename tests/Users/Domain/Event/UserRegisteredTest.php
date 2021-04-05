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
final class UserRegisteredTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new UserRegistered('user-1', 'alan.bem@example.com', 'hash', 'salt', $now = new \DateTimeImmutable());

        self::assertSame('user-1', $event->userId());
        self::assertSame('alan.bem@example.com', $event->email());
        self::assertSame('hash', $event->passwordHash());
        self::assertSame('salt', $event->salt());
        self::assertEquals($now, $event->registeredAt());
        self::assertNotSame($now, $event->registeredAt());
    }
}
