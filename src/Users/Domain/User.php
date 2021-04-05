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

use Streak\Application\Command;
use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Event;
use Users\Domain\Command as Commands;
use Users\Domain\Event as Events;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
final class User implements Event\Sourced\AggregateRoot, CommandHandler
{
    use Event\Sourced\AggregateRoot\Identification;
    use AggregateRoot\Comparison;
    use Event\Sourcing;
    use Command\Handling;

    private PasswordHasher $encoder;
    private SaltGenerator $saltshaker;
    private Clock $clock;

    public function __construct(User\Id $id, PasswordHasher $encoder, SaltGenerator $saltshaker, Clock $clock)
    {
        $this->identifyBy($id);

        $this->encoder = $encoder;
        $this->saltshaker = $saltshaker;
        $this->clock = $clock;
    }

    public function userId() : string
    {
        return $this->aggregateRootId()->toString();
    }

    /**
     * @see User::applyUserRegistered()
     */
    public function handleRegisterUser(Commands\RegisterUser $command) : void
    {
        Assert::email($command->email(), 'Email is missing.');
        Assert::notEmpty($command->password(), 'Password is missing.');

        $salt = $this->saltshaker->generate();
        $hash = $this->encoder->encode($command->password(), $salt);

        $this->apply(new Events\UserRegistered($this->userId(), $command->email(), $hash, $salt, $this->clock->now()));
    }

    /**
     * @see User::handleRegisterUser()
     */
    private function applyUserRegistered(Events\UserRegistered $event) : void
    {
    }
}
