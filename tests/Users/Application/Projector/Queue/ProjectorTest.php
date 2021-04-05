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

namespace Users\Application\Projector\Queue;

use Monolog\Test\TestCase;
use Productivity\Application\Sensor\UsersEvents\Sensor;
use Streak\Domain\Event;
use Streak\Domain\Event\Envelope;
use Streak\Infrastructure\EventStore\InMemoryEventStore;
use Users\Domain\Event\UserRegistered;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Projector\Queue\Projector
 */
final class ProjectorTest extends TestCase
{
    private Projector\Queue $queue;

    protected function setUp() : void
    {
        $this->queue = $this->createMock(Projector\Queue::class);
    }

    public function testProjector() : void
    {
        $event = Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'alan.bem@example.com', 'hash', 'salt', $now = new \DateTimeImmutable()), new Sensor\Id());

        $this->queue
            ->expects(self::once())
            ->method('send')
            ->with($event->uuid()->toString(), 'user_registered', ['user_id' => '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'email' => 'alan.bem@example.com', 'registered_at' => $now->format('Y-m-d H:i:s.u P')]);

        $projector = new Projector(new Projector\Id(), $this->queue);

        self::assertTrue($projector->on($event));
    }

    public function testProjectorWithWrongEvent() : void
    {
        $event = Envelope::new($this->createMock(Event::class), new Sensor\Id());

        $this->queue
            ->expects(self::never())
            ->method(self::anything());

        $projector = new Projector(new Projector\Id(), $this->queue);

        self::assertFalse($projector->on($event));
    }

    public function testPickingFirstEvent()
    {
        $event1 = Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());
        $event2 = Envelope::new(new UserRegistered('d7689177-bcbf-4617-a686-dd5f5fc22f10', 'jaxweb@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());
        $event3 = Envelope::new(new UserRegistered('6973e772-19f1-4334-b1e3-e0f7217a6574', 'ebassi@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());
        $event4 = Envelope::new(new UserRegistered('c70a16c7-a43f-4c62-8d4d-03f849661902', 'biglou@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());

        $store = new InMemoryEventStore();
        $store->add($event1, $event2, $event3, $event4);

        $projector = new Projector(new Projector\Id(), $this->queue);
        $picked = $projector->pick($store);

        self::assertTrue($picked->equals($event1));
    }

    public function testFilteringEvents()
    {
        $event1 = Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());
        $event2 = Envelope::new(new UserRegistered('d7689177-bcbf-4617-a686-dd5f5fc22f10', 'jaxweb@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());
        $event3 = Envelope::new(new UserRegistered('6973e772-19f1-4334-b1e3-e0f7217a6574', 'ebassi@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new Sensor\Id());
        $event4 = Envelope::new($this->createMock(Event::class), new Sensor\Id());

        $store = new InMemoryEventStore();
        $store->add($event1, $event2, $event3, $event4);
        $stream = $store->stream();
        $expected = $stream->only(UserRegistered::class);

        $projector = new Projector(new Projector\Id(), $this->queue);
        $filtered = $projector->filter($stream);

        self::assertEquals($expected, $filtered);
    }
}
