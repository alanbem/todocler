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

namespace Productivity\Domain\Project\Task;

use PHPUnit\Framework\TestCase;
use Streak\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Project\Task\Id
 */
final class IdTest extends TestCase
{
    public function testToString() : void
    {
        $id1 = new Id('id-1');
        $id2 = Id::fromString('id-2');

        self::assertSame('id-1', $id1->toString());
        self::assertSame('id-2', $id2->toString());
    }

    public function testEquals() : void
    {
        $id1a = new Id('id-1');
        $id1b = Id::fromString('id-1');

        self::assertTrue($id1a->equals($id1b));
        self::assertTrue($id1b->equals($id1a));
    }

    public function testNotEqual() : void
    {
        $id1 = new Id('id-1');
        $id2 = Id::fromString('id-2');

        self::assertFalse($id1->equals($id2));
        self::assertFalse($id2->equals($id1));
        self::assertFalse($id1->equals($this->createMock(Domain\Id::class)));
    }
}
