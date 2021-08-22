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

namespace Users\Domain;

use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Command;
use Streak\Domain\CommandHandler;
use Streak\Domain\Event\Sourced as EventSourced;
use Users\Domain\Command as Commands;
use Users\Domain\Event as Events;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Domain\User\Factory
 */
final class User implements EventSourced\AggregateRoot, CommandHandler
{
    use AggregateRoot\Comparison;
    use AggregateRoot\EventSourcing;
    use AggregateRoot\Identification;
    use Command\Handling;

    private PasswordHasher $hasher;
    private Clock $clock;

    public function __construct(User\Id $id, PasswordHasher $encoder, Clock $clock)
    {
        $this->identifyBy($id);

        $this->hasher = $encoder;
        $this->clock = $clock;
    }

    public function userId() : string
    {
        return $this->id()->toString();
    }

    /**
     * @see User::applyUserRegistered()
     */
    public function handleRegisterUser(Commands\RegisterUser $command) : void
    {
        Assert::email($command->email(), 'Email is missing.');
        Assert::notEmpty($command->password(), 'Password is missing.');

        $hashedPassword = $this->hasher->hash($command->password());

        $this->apply(new Events\UserRegistered($this->userId(), $command->email(), $hashedPassword, $this->clock->now()));
    }

    /**
     * @see User::handleRegisterUser()
     */
    private function applyUserRegistered(Events\UserRegistered $event) : void
    {
    }
}
