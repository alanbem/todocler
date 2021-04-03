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

namespace Users\Application\Projector\RegisteredUsers\Doctrine\Entity;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser
 */
final class RegisteredUserTest extends TestCase
{
    public function testUser() : void
    {
        $user = new RegisteredUser('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6', 'alan.bem@example.com', 'password', 'salt', $now = new \DateTimeImmutable());

        self::assertSame('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6', $user->getId());
        self::assertSame('alan.bem@example.com', $user->getUsername());
        self::assertSame('password', $user->getPassword());
        self::assertSame('salt', $user->getSalt());
        self::assertSame([], $user->getRoles());
        self::assertSame($now, $user->getRegisteredAt());

        $user->eraseCredentials();

        self::assertSame('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6', $user->getId());
        self::assertSame('alan.bem@example.com', $user->getUsername());
        self::assertSame('', $user->getPassword());
        self::assertSame('', $user->getSalt());
        self::assertSame([], $user->getRoles());
        self::assertSame($now, $user->getRegisteredAt());
    }
}
