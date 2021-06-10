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

namespace Users\Application\Projector\RegisteredUsers;

use Doctrine\ORM\EntityManagerInterface;
use Streak\Domain\Event\Envelope;
use Streak\Infrastructure\Domain\EventStore\InMemoryEventStore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Users\Application\Projector\RegisteredUsers;
use Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser;
use Users\Application\Query\FindUser;
use Users\Application\Query\IsUserRegistered;
use Users\Domain\Event\UserRegistered;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Application\Projector\RegisteredUsers\Projector
 * @covers \Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser
 * @covers \Shared\Application\Projector\Doctrine\EntityManagerProjector
 */
final class ProjectorTest extends KernelTestCase
{
    private RegisteredUsers\Projector $projector;

    protected function setUp() : void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $manager */
        $manager = $kernel->getContainer()->get('doctrine.orm.registered_users_projection_entity_manager');

        $this->projector = new RegisteredUsers\Projector(new RegisteredUsers\Projector\Id(), $manager);
        $this->projector->reset(); // reset database on every test
    }

    public function testProjector()
    {
        // no user
        $exists = $this->projector->handleQuery(new IsUserRegistered('alan.bem@example.com'));

        self::assertFalse($exists);

        // freshly registered user
        $event = Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'alan.bem@example.com', 'hash', $now = new \DateTimeImmutable()), new User\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $this->projector->on($event);

        $exists = $this->projector->handleQuery(new IsUserRegistered('alan.bem@example.com'));
        $user = $this->projector->handleQuery(new FindUser('alan.bem@example.com'));

        self::assertTrue($exists);
        self::assertEquals(new RegisteredUser('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'alan.bem@example.com', 'hash', $now), $user);

        // check protection for 1 in a million chance of registration of same user/email
        $event = Envelope::new(new UserRegistered('d7689177-bcbf-4617-a686-dd5f5fc22f10', 'alan.bem@example.com', 'another-hash', new \DateTimeImmutable()), new User\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'));
        $this->projector->on($event);

        $exists = $this->projector->handleQuery(new IsUserRegistered('alan.bem@example.com'));
        $user = $this->projector->handleQuery(new FindUser('alan.bem@example.com'));

        self::assertTrue($exists);
        self::assertEquals(new RegisteredUser('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'alan.bem@example.com', 'hash', $now), $user);
    }

    public function testPickingFirstEvent()
    {
        $event1 = Envelope::new(new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', 'another-hash', new \DateTimeImmutable()), new User\Id('8e5ebf2b-f78c-430d-b15f-0f3e710b284b'), 1);
        $event2 = Envelope::new(new UserRegistered('d7689177-bcbf-4617-a686-dd5f5fc22f10', 'jaxweb@example.com', 'another-hash', new \DateTimeImmutable()), new User\Id('d7689177-bcbf-4617-a686-dd5f5fc22f10'), 1);
        $event3 = Envelope::new(new UserRegistered('6973e772-19f1-4334-b1e3-e0f7217a6574', 'ebassi@example.com', 'another-hash', new \DateTimeImmutable()), new User\Id('6973e772-19f1-4334-b1e3-e0f7217a6574'), 1);
        $event4 = Envelope::new(new UserRegistered('c70a16c7-a43f-4c62-8d4d-03f849661902', 'biglou@example.com', 'another-hash', new \DateTimeImmutable()), new User\Id('c70a16c7-a43f-4c62-8d4d-03f849661902'), 1);

        $store = new InMemoryEventStore();
        $store->add($event1, $event2, $event3, $event4);
        $picked = $this->projector->pick($store);

        self::assertTrue($picked->equals($event1));
    }
}
