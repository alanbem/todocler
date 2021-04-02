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

namespace Users\Application\Query;

use PHPUnit\Framework\TestCase;
use Users\Application\Projector\RegisteredUsers\Projector;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Query\IsUserRegistered
 */
final class IsUserRegisteredTest extends TestCase
{
    public function testQuery() : void
    {
        $query = new IsUserRegistered('alan.bem@example.com');

        $this->assertSame('alan.bem@example.com', $query->email());
        $this->assertEquals(new Projector\Id(), $query->listenerId());
    }
}
