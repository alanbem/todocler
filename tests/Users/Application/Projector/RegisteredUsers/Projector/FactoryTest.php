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

namespace Users\Application\Projector\RegisteredUsers\Projector;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Streak\Domain\Event;
use Streak\Domain\Event\Listener;
use Streak\Domain\Exception\InvalidIdGiven;
use Users\Application\Projector\RegisteredUsers\Projector;
use Users\Domain\Event\UserRegistered;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Projector\RegisteredUsers\Projector\Factory
 */
class FactoryTest extends TestCase
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

        $this->assertEquals(new Projector($id, $this->manager), $aggregate);
    }

    public function testWrongId()
    {
        $factory = new Projector\Factory($this->manager);
        $id = $this->createMock(Listener\Id::class);

        $this->expectExceptionObject(new InvalidIdGiven($id));

        $factory->create($id);
    }

    public function testCreatingOnEvent() : void
    {
        $factory = new Projector\Factory($this->manager);
        $event = Event\Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', 'another-hash', 'salt', new \DateTimeImmutable()), new User\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);

        $projector = $factory->createFor($event);

        $this->assertEquals(new Projector(new Projector\Id(), $this->manager), $projector);
    }
}
