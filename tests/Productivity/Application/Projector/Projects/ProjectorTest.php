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

namespace Productivity\Application\Projector\Projects;

use Doctrine\ORM\EntityManagerInterface;
use Productivity\Application\Projector\Projects;
use Productivity\Application\Query as Queries;
use Productivity\Domain\Event\ProjectCreated;
use Productivity\Domain\Event\ProjectRemoved;
use Productivity\Domain\Event\ProjectRenamed;
use Productivity\Domain\Event\TaskCompleted;
use Productivity\Domain\Event\TaskCreated;
use Productivity\Domain\Event\TaskRemoved;
use Productivity\Domain\Project;
use Streak\Domain\Event\Envelope;
use Streak\Infrastructure\Domain\EventStore\InMemoryEventStore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Projects\Projector
 * @covers \Productivity\Application\Projector\Projects\Doctrine\Entity\Project
 * @covers \Productivity\Application\Projector\Projects\Doctrine\Entity\Task
 * @covers \Shared\Application\Projector\Doctrine\EntityManagerProjector
 */
final class ProjectorTest extends KernelTestCase
{
    private Projects\Projector $projector;

    protected function setUp() : void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $manager */
        $manager = $kernel->getContainer()->get('doctrine.orm.projects_projection_entity_manager');

        $this->projector = new Projects\Projector(new Projects\Projector\Id(), $manager);
        $this->projector->reset(); // reset database on every test
    }

    public function testProjector() : void
    {
        $projects = $this->projector->handleQuery(new Queries\BrowseProjects());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        self::assertEmpty($projects);
        self::assertEmpty($tasks);

        $event = Envelope::new(new ProjectCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first project', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $this->projector->on($event);

        $event = Envelope::new(new ProjectRenamed('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'Project #1', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 2);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '10a6fea1-6f39-4a65-bfa2-4d84b34a277a', 'Project #1 - Task #1', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 3);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCompleted('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '10a6fea1-6f39-4a65-bfa2-4d84b34a277a', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 4);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '60492a0b-912d-4873-8c08-653b70398a13', 'Project #1 - Task #2', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '77aa67fd-4b63-4545-b650-46f691f22000', 'Project #1 - Task #3', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 6);
        $this->projector->on($event);

        $event = Envelope::new(new ProjectCreated('39763bae-d28d-41a3-b360-758a10fcad27', 'My first project', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 1);
        $this->projector->on($event);

        $event = Envelope::new(new ProjectRenamed('39763bae-d28d-41a3-b360-758a10fcad27', 'Project #2', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 2);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('39763bae-d28d-41a3-b360-758a10fcad27', '63665679-a039-4349-b535-f50f35859b3b', 'Project #2 - Task #1', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 3);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('39763bae-d28d-41a3-b360-758a10fcad27', '71ee2742-30f1-4400-86ba-0ea1076e31e7', 'Project #2 - Task #2', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 4);
        $this->projector->on($event);

        $event = Envelope::new(new TaskCreated('39763bae-d28d-41a3-b360-758a10fcad27', 'abc72ff4-0892-4b9f-b247-d1cf9781313b', 'Project #2 - Task #3', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new ProjectCreated('214d95cc-9c91-4b31-ba3f-60c31cbac370', 'Project #3', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 1);
        $this->projector->on($event);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        self::assertCount(3, $projects);
        self::assertCount(6, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));

        self::assertCount(1, $projects);
        self::assertCount(3, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('39763bae-d28d-41a3-b360-758a10fcad27'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('39763bae-d28d-41a3-b360-758a10fcad27'));

        self::assertCount(2, $projects);
        self::assertCount(3, $tasks);

        $event = Envelope::new(new TaskRemoved('39763bae-d28d-41a3-b360-758a10fcad27', '63665679-a039-4349-b535-f50f35859b3b', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new TaskRemoved('39763bae-d28d-41a3-b360-758a10fcad27', '71ee2742-30f1-4400-86ba-0ea1076e31e7', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new TaskRemoved('39763bae-d28d-41a3-b360-758a10fcad27', 'abc72ff4-0892-4b9f-b247-d1cf9781313b', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 5);
        $this->projector->on($event);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        self::assertCount(3, $projects);
        self::assertCount(3, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));

        self::assertCount(1, $projects);
        self::assertCount(3, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('39763bae-d28d-41a3-b360-758a10fcad27'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('39763bae-d28d-41a3-b360-758a10fcad27'));

        self::assertCount(2, $projects);
        self::assertCount(0, $tasks);

        $event = Envelope::new(new TaskRemoved('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '10a6fea1-6f39-4a65-bfa2-4d84b34a277a', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 3);
        $this->projector->on($event);

        $event = Envelope::new(new TaskRemoved('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '60492a0b-912d-4873-8c08-653b70398a13', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 5);
        $this->projector->on($event);

        $event = Envelope::new(new TaskRemoved('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '77aa67fd-4b63-4545-b650-46f691f22000', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 6);
        $this->projector->on($event);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        self::assertCount(3, $projects);
        self::assertCount(0, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));

        self::assertCount(1, $projects);
        self::assertCount(0, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('39763bae-d28d-41a3-b360-758a10fcad27'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('39763bae-d28d-41a3-b360-758a10fcad27'));

        self::assertCount(2, $projects);
        self::assertCount(0, $tasks);

        $event = Envelope::new(new ProjectRemoved('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $this->projector->on($event);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        self::assertCount(2, $projects);
        self::assertCount(0, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));

        self::assertCount(0, $projects);
        self::assertCount(0, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('39763bae-d28d-41a3-b360-758a10fcad27'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('39763bae-d28d-41a3-b360-758a10fcad27'));

        self::assertCount(2, $projects);
        self::assertCount(0, $tasks);

        $event = Envelope::new(new ProjectRemoved('39763bae-d28d-41a3-b360-758a10fcad27', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 1);
        $this->projector->on($event);

        $event = Envelope::new(new ProjectRemoved('214d95cc-9c91-4b31-ba3f-60c31cbac370', '39763bae-d28d-41a3-b360-758a10fcad27', new \DateTimeImmutable()), new Project\Id('39763bae-d28d-41a3-b360-758a10fcad27'), 1);
        $this->projector->on($event);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects());
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks());

        self::assertCount(0, $projects);
        self::assertCount(0, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));

        self::assertCount(0, $projects);
        self::assertCount(0, $tasks);

        $projects = $this->projector->handleQuery(new Queries\BrowseProjects('39763bae-d28d-41a3-b360-758a10fcad27'));
        $tasks = $this->projector->handleQuery(new Queries\BrowseTasks('39763bae-d28d-41a3-b360-758a10fcad27'));

        self::assertCount(0, $projects);
        self::assertCount(0, $tasks);
    }

    public function testPickingFirstEvent() : void
    {
        $event1 = Envelope::new(new ProjectCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first project', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $event2 = Envelope::new(new ProjectRenamed('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My edited name', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 2);
        $event3 = Envelope::new(new TaskCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '01a9b3a9-685b-4dff-9e3a-b66f14bed6b5', 'My edited name', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 3);
        $event4 = Envelope::new(new TaskCompleted('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', '01a9b3a9-685b-4dff-9e3a-b66f14bed6b5', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 4);

        $store = new InMemoryEventStore();
        $store->add($event1, $event2, $event3, $event4);
        $picked = $this->projector->pick($store);

        self::assertTrue($picked->equals($event1));
    }
}
