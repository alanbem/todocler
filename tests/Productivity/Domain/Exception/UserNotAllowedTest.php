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

namespace Productivity\Domain\Exception;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Exception\UserNotAllowed
 */
final class UserNotAllowedTest extends TestCase
{
    public function testException() : void
    {
        $exception = new UserNotAllowed('user-1');

        $this->assertSame('User "user-1" is not allowed.', $exception->getMessage());
        $this->assertSame('user-1', $exception->userId());
    }
}
