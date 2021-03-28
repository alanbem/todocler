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

namespace Users\Infrastructure;

use PHPUnit\Framework\TestCase;
use Streak\Application\QueryBus;
use Users\Application\Query as Queries;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\SynchronousFacade
 */
class SynchronousFacadeTest extends TestCase
{
    private QueryBus $bus;

    protected function setUp() : void
    {
        $this->bus = $this->createMock(QueryBus::class);
    }

    public function testFacade()
    {
        $facade = new SynchronousFacade($this->bus);

        $this->bus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [new Queries\IsUserRegistered('alan.bem@gmail.com')],
                [new Queries\IsUserRegistered('me@example.com')],
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true,
            );

        $this->assertFalse($facade->isUserRegistered('alan.bem@gmail.com'));
        $this->assertTrue($facade->isUserRegistered('me@example.com'));
    }
}
