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

namespace Productivity\Application\Projector\Lists\Projector;

use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Lists\Projector;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Lists\Projector\Id
 */
class IdTest extends TestCase
{
    public function testId() : void
    {
        $id1 = new Projector\Id();
        $id2 = Projector\Id::fromString('c4b6d1ee-f7f3-4a41-9cae-cae4e29403e6'); // given uuid is ignored

        $this->assertTrue($id1->equals($id1));
        $this->assertTrue($id2->equals($id2));
        $this->assertTrue($id1->equals($id2));
        $this->assertTrue($id2->equals($id1));

        $this->assertSame('00000000-0000-0000-0000-000000000000', $id1->toString());
        $this->assertSame('00000000-0000-0000-0000-000000000000', $id2->toString());

        $this->assertFalse($id1->equals(new \stdClass()));
    }
}
