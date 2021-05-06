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

namespace Users\Application\Projector\Queue\Projector;

use PHPUnit\Framework\TestCase;
use Streak\Domain\Event;
use Streak\Domain\Event\Listener;
use Streak\Domain\Exception\InvalidIdGiven;
use Users\Application\Projector\Queue\Projector;
use Users\Domain\Event\UserRegistered;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Projector\Queue\Projector\Factory
 */
final class FactoryTest extends TestCase
{
    private Queue $queue;

    protected function setUp() : void
    {
        $this->queue = $this->createMock(Queue::class);
    }

    public function testFactory() : void
    {
        $factory = new Projector\Factory($this->queue);
        $id = new Projector\Id();

        $aggregate = $factory->create($id);

        self::assertEquals(new Projector($id, $this->queue), $aggregate);
    }

    public function testWrongId()
    {
        $factory = new Projector\Factory($this->queue);
        $id = $this->createMock(Listener\Id::class);

        $this->expectExceptionObject(new InvalidIdGiven($id));

        $factory->create($id);
    }

    public function testCreatingOnEvent() : void
    {
        $factory = new Projector\Factory($this->queue);
        $event = Event\Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', 'another-hash', new \DateTimeImmutable()), new User\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);

        $projector = $factory->createFor($event);

        self::assertEquals(new Projector(new Projector\Id(), $this->queue), $projector);
    }
}
