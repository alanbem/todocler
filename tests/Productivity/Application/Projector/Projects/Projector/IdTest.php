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

namespace Productivity\Application\Projector\Projects\Projector;

use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Projects\Projector;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Projects\Projector\Id
 */
final class IdTest extends TestCase
{
    public function testId() : void
    {
        $id1 = new Projector\Id();
        $id2 = Projector\Id::fromString('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6'); // given uuid is ignored

        self::assertTrue($id1->equals($id1));
        self::assertTrue($id2->equals($id2));
        self::assertTrue($id1->equals($id2));
        self::assertTrue($id2->equals($id1));

        self::assertSame('00000000-0000-0000-0000-000000000000', $id1->toString());
        self::assertSame('00000000-0000-0000-0000-000000000000', $id2->toString());

        self::assertFalse($id1->equals(new \stdClass()));
    }
}
