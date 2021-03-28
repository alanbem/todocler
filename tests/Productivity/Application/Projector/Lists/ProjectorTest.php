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

namespace Productivity\Application\Projector\Lists;

use Doctrine\ORM\EntityManagerInterface;
use Productivity\Application\Projector\Lists;
use Productivity\Application\Query as Queries;
use Productivity\Domain\Checklist;
use Productivity\Domain\Event\ListCreated;
use Productivity\Domain\Event\ListRenamed;
use Productivity\Domain\Event\TaskCompleted;
use Productivity\Domain\Event\TaskCreated;
use Streak\Domain\Event\Envelope;
use Streak\Infrastructure\EventStore\InMemoryEventStore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Lists\Projector
 * @covers \Productivity\Application\Projector\Lists\Doctrine\Entity\Checklist
 * @covers \Productivity\Application\Projector\Lists\Doctrine\Entity\Task
 * @covers \Shared\Application\Projector\Doctrine\EntityManagerProjector
 */
class ProjectorTest extends KernelTestCase
{
    private Lists\Projector $projector;

    protected function setUp() : void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $manager */
        $manager = $kernel->getContainer()->get('doctrine.orm.lists_projection_entity_manager');

        $this->projector = new Lists\Projector(new Lists\Projector\Id(), $manager);
        $this->projector->reset(); // reset database on every test
    }

    public function testProjector() : void
    {
        $lists = $this->projector->handleQuery(new Queries\BrowseChecklists());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        $this->assertEmpty($lists);
        $this->assertEmpty($tasks);

        $event = Envelope::new(new ListCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first list', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', $created = new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $this->projector->on($event);

        $event = Envelope::new(new ListRenamed('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'List #1', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', $updated = new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 2);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '10a6fea1-6f39-4a65-bfa2-4d84b34a277a', 'List #1 - Task #1', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 3);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCompleted('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '10a6fea1-6f39-4a65-bfa2-4d84b34a277a', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 4);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '60492a0b-912d-4873-8c08-653b70398a13', 'List #1 - Task #2', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '77aa67fd-4b63-4545-b650-46f691f22000', 'List #1 - Task #3', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 6);
        $this->projector->on($event);

        $event = Envelope::new(new ListCreated('39763bae-d28d-41a3-b360-758a10fcad27', 'My first list', '39763bae-d28d-41a3-b360-758a10fcad27', $created = new \DateTimeImmutable()), new Checklist\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 1);
        $this->projector->on($event);

        $event = Envelope::new(new ListRenamed('39763bae-d28d-41a3-b360-758a10fcad27', 'List #2', '39763bae-d28d-41a3-b360-758a10fcad27', $updated = new \DateTimeImmutable()), new Checklist\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 2);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('39763bae-d28d-41a3-b360-758a10fcad27', '63665679-a039-4349-b535-f50f35859b3b', 'List #2 - Task #1', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Checklist\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 3);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('39763bae-d28d-41a3-b360-758a10fcad27', '71ee2742-30f1-4400-86ba-0ea1076e31e7', 'List #2 - Task #2', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Checklist\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 4);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('39763bae-d28d-41a3-b360-758a10fcad27', 'abc72ff4-0892-4b9f-b247-d1cf9781313b', 'List #2 - Task #3', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Checklist\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new ListCreated('214d95cc-9c91-4b31-ba3f-60c31cbac370', 'List #3', '39763bae-d28d-41a3-b360-758a10fcad27', $created = new \DateTimeImmutable()), new Checklist\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 1);
        $this->projector->on($event);

        $lists = $this->projector->handleQuery(new Queries\BrowseChecklists());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        $this->assertCount(3, $lists);
        $this->assertCount(6, $tasks);

        $lists = $this->projector->handleQuery(new Queries\BrowseChecklists('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));

        $this->assertCount(1, $lists);
        $this->assertCount(3, $tasks);

        $lists = $this->projector->handleQuery(new Queries\BrowseChecklists('39763bae-d28d-41a3-b360-758a10fcad27'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('39763bae-d28d-41a3-b360-758a10fcad27'));

        $this->assertCount(2, $lists);
        $this->assertCount(3, $tasks);
    }

    public function testPickingFirstEvent()
    {
        $event1 = Envelope::new(new ListCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first list', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $event2 = Envelope::new(new ListRenamed('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My edited name', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 2);
        $event3 = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '01a9b3a9-685b-4dff-9e3a-b66f14bed6b5', 'My edited name', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 3);
        $event4 = Envelope::new(new TaskCompleted('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '01a9b3a9-685b-4dff-9e3a-b66f14bed6b5', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Checklist\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 4);

        $store = new InMemoryEventStore();
        $store->add($event1, $event2, $event3, $event4);
        $picked = $this->projector->pick($store);

        $this->assertTrue($picked->equals($event1));
    }
}
