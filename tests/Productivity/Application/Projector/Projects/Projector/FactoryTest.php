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

namespace Productivity\Application\Projector\Projects\Projector;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Projects\Projector;
use Productivity\Domain\Event\ProjectCreated;
use Productivity\Domain\Project;
use Streak\Domain\Event\Envelope;
use Streak\Domain\Event\Listener;
use Streak\Domain\Exception\InvalidIdGiven;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Projector\Projects\Projector\Factory
 */
final class FactoryTest extends TestCase
{
    private EntityManagerInterface $manager;

    protected function setUp() : void
    {
        $this->manager = $this->createMock(EntityManagerInterface::class);
    }

    public function testFactory() : void
    {
        $factory = new Projector\Factory($this->manager);
        $id = new Projector\Id();

        $aggregate = $factory->create($id);

        self::assertEquals(new Projector($id, $this->manager), $aggregate);
    }

    public function testWrongId() : void
    {
        $factory = new Projector\Factory($this->manager);
        $id = $this->createMock(Listener\Id::class);

        $this->expectExceptionObject(new InvalidIdGiven($id));

        $factory->create($id);
    }

    public function testCreatingOnEvent() : void
    {
        $factory = new Projector\Factory($this->manager);
        $event = Envelope::new(new ProjectCreated('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'My first project', '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', new \DateTimeImmutable()), new Project\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);

        $projector = $factory->createFor($event);

        self::assertEquals(new Projector(new Projector\Id(), $this->manager), $projector);
    }
}
