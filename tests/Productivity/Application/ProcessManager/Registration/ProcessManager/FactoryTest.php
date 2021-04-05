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

namespace Productivity\Application\ProcessManager\Registration\ProcessManager;

use PHPUnit\Framework\TestCase;
use Productivity\Application\ProcessManager\Registration\ProcessManager;
use Productivity\Domain\Checklist;
use Productivity\Domain\Event\ListCreated;
use Streak\Application\CommandBus;
use Streak\Domain\Event;
use Streak\Domain\Event\Listener;
use Streak\Domain\Exception\InvalidIdGiven;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\ProcessManager\Registration\ProcessManager\Factory
 */
final class FactoryTest extends TestCase
{
    private CommandBus $bus;

    protected function setUp() : void
    {
        $this->bus = $this->createMock(CommandBus::class);
    }

    public function testFactory() : void
    {
        $factory = new ProcessManager\Factory($this->bus, 'name');
        $id = new ProcessManager\Id();

        $aggregate = $factory->create($id);

        self::assertEquals(new ProcessManager($id, $this->bus, 'name'), $aggregate);
    }

    public function testWrongId()
    {
        $factory = new ProcessManager\Factory($this->bus, 'name');
        $id = $this->createMock(Listener\Id::class);

        $this->expectExceptionObject(new InvalidIdGiven($id));

        $factory->create($id);
    }

    public function testCreatingOnEvent() : void
    {
        $factory = new ProcessManager\Factory($this->bus, 'name');
        $event = Event\Envelope::new(new ListCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first list', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);

        $projector = $factory->createFor($event);

        self::assertEquals(new ProcessManager(new ProcessManager\Id(), $this->bus, 'name'), $projector);
    }
}
