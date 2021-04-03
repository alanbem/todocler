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

namespace Productivity\Application\Event;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Event\UserRegistered
 */
final class UserRegisteredTest extends TestCase
{
    public function testEvent() : void
    {
        $event = new UserRegistered('user-1', 'alan.bem@example.com', $now = new \DateTimeImmutable());

        self::assertSame('user-1', $event->userId());
        self::assertSame('alan.bem@example.com', $event->email());
        self::assertEquals($now, $event->registeredAt());
        self::assertNotSame($now, $event->registeredAt());
    }
}
