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

namespace Users\Domain\User;

use PHPUnit\Framework\TestCase;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Exception\InvalidAggregateIdGiven;
use Users\Domain\PasswordHasher;
use Users\Domain\SaltGenerator;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Domain\User\Factory
 */
final class FactoryTest extends TestCase
{
    private PasswordHasher $encoder;
    private SaltGenerator $saltshaker;
    private Clock $clock;

    protected function setUp() : void
    {
        $this->encoder = $this->createMock(PasswordHasher::class);
        $this->saltshaker = $this->createMock(SaltGenerator::class);
        $this->clock = $this->createMock(Clock::class);
    }

    public function testFactory() : void
    {
        $factory = new User\Factory($this->encoder, $this->saltshaker, $this->clock);
        $id = new User\Id('list-1');

        $aggregate = $factory->create($id);

        self::assertEquals(new User($id, $this->encoder, $this->saltshaker, $this->clock), $aggregate);
    }

    public function testWrongId()
    {
        $factory = new User\Factory($this->encoder, $this->saltshaker, $this->clock);
        $id = $this->createMock(AggregateRoot\Id::class);

        $this->expectExceptionObject(new InvalidAggregateIdGiven($id));

        $factory->create($id);
    }
}
