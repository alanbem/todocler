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

namespace Productivity\Interfaces\Rest\ApiPlatform\Messenger\Stamp;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Stamp\RegisteredUserStamp
 */
final class RegisteredUserStampTest extends TestCase
{
    public function testStamp() : void
    {
        $user = new \stdClass();
        $stamp = new RegisteredUserStamp($user);

        $actual = $stamp->user();

        $this->assertSame($user, $actual);
    }
}
