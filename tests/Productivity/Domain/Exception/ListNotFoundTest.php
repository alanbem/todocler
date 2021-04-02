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
 * @covers \Productivity\Domain\Exception\ListNotFound
 */
final class ListNotFoundTest extends TestCase
{
    public function testException() : void
    {
        $exception = new ListNotFound('list-1');

        $this->assertSame('List "list-1" not found.', $exception->getMessage());
        $this->assertSame('list-1', $exception->listId());
    }
}
