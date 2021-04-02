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

namespace Productivity\Domain\Checklist;

use PHPUnit\Framework\TestCase;
use Streak\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Checklist\Id
 */
final class IdTest extends TestCase
{
    public function testToString() : void
    {
        $id1 = new Id('id-1');
        $id2 = Id::fromString('id-2');

        $this->assertSame('id-1', $id1->toString());
        $this->assertSame('id-2', $id2->toString());
    }

    public function testEquals()
    {
        $id1a = new Id('id-1');
        $id1b = Id::fromString('id-1');

        $this->assertTrue($id1a->equals($id1b));
        $this->assertTrue($id1b->equals($id1a));
    }

    public function testNotEqual()
    {
        $id1 = new Id('id-1');
        $id2 = Id::fromString('id-2');

        $this->assertFalse($id1->equals($id2));
        $this->assertFalse($id2->equals($id1));
        $this->assertFalse($id1->equals($this->createMock(Domain\Id::class)));
    }
}
