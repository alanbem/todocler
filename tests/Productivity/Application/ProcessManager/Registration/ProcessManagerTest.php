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

namespace Productivity\Application\ProcessManager\Registration;

use Productivity\Application\Command\CreateList;
use Productivity\Application\ProcessManager\Registration;
use Streak\Application\CommandBus;
use Streak\Domain\Event;
use Streak\Domain\Event\Envelope;
use Streak\Infrastructure\EventStore\InMemoryEventStore;
use Streak\Infrastructure\Testing\Listener\TestCase;
use Users\Domain\Event\UserRegistered;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\ProcessManager\Registration\ProcessManager
 */
final class ProcessManagerTest extends TestCase
{
    private const DEFAULT_NAME = 'My first list';

    private CommandBus $bus;
    private Registration\ProcessManager $manager;

    protected function setUp() : void
    {
        $this->bus = $this->createMock(CommandBus::class);
        $this->manager = new Registration\ProcessManager(new Registration\ProcessManager\Id(), $this->bus, self::DEFAULT_NAME);
    }

    public function testCreatingListForFreshlyRegisteredUser()
    {
        $this
            ->given()
            ->when(
                new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'alan.bem@example.com', 'hash', 'salt', new \DateTimeImmutable()),
            )
            ->then(
                new CreateList('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first list', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b')
            )
            ->assert();
    }

    public function testPickingFirstEvent()
    {
        $event1 = Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new User\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $event2 = Envelope::new(new UserRegistered('d7689177-bcbf-4617-a686-dd5f5fc22f10', 'jaxweb@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new User\Id('d7689177-bcbf-4617-a686-dd5f5fc22f10'), 1);
        $event3 = Envelope::new(new UserRegistered('6973e772-19f1-4334-b1e3-e0f7217a6574', 'ebassi@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new User\Id('6973e772-19f1-4334-b1e3-e0f7217a6574'), 1);
        $event4 = Envelope::new(new UserRegistered('c70a16c7-a43f-4c62-8d4d-03f849661902', 'biglou@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new User\Id('c70a16c7-a43f-4c62-8d4d-03f849661902'), 1);

        $store = new InMemoryEventStore();
        $store->add($event1, $event2, $event3, $event4);
        $picked = $this->manager->pick($store);

        $this->assertTrue($picked->equals($event1));
    }

    public function createFactory(CommandBus $bus) : Event\Listener\Factory
    {
        return new Registration\ProcessManager\Factory($bus, self::DEFAULT_NAME);
    }
}
