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
class RegisteredUserTest extends TestCase
{
    public function testUser() : void
    {
        $user = new RegisteredUser('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6', 'alan.bem@example.com', 'password', 'salt', $now = new \DateTimeImmutable());

        $this->assertSame('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6', $user->getId());
        $this->assertSame('alan.bem@example.com', $user->getUsername());
        $this->assertSame('password', $user->getPassword());
        $this->assertSame('salt', $user->getSalt());
        $this->assertSame([], $user->getRoles());
        $this->assertSame($now, $user->getRegisteredAt());

        $user->eraseCredentials();

        $this->assertSame('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6', $user->getId());
        $this->assertSame('alan.bem@example.com', $user->getUsername());
        $this->assertSame('', $user->getPassword());
        $this->assertSame('', $user->getSalt());
        $this->assertSame([], $user->getRoles());
        $this->assertSame($now, $user->getRegisteredAt());
    }
}
