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

namespace Productivity\Domain\Project;

use PHPUnit\Framework\TestCase;
use Productivity\Domain\Project;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Exception\InvalidAggregateIdGiven;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Domain\Project\Factory
 */
final class FactoryTest extends TestCase
{
    private Clock $clock;

    protected function setUp() : void
    {
        $this->clock = $this->createMock(Clock::class);
    }

    public function testFactory() : void
    {
        $factory = new Project\Factory($this->clock);
        $id = new Project\Id('project-1');

        $aggregate = $factory->create($id);

        self::assertEquals(new Project($id, $this->clock), $aggregate);
    }

    public function testWrongId() : void
    {
        $factory = new Project\Factory($this->clock);
        $id = $this->createMock(AggregateRoot\Id::class);

        $this->expectExceptionObject(new InvalidAggregateIdGiven($id));

        $factory->create($id);
    }
}
