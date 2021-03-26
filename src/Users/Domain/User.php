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
use Users\Application\Command as Commands;
use Users\Domain\Event as Events;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class User implements Event\Sourced\AggregateRoot, CommandHandler
{
    use Event\Sourced\AggregateRoot\Identification;
    use AggregateRoot\Comparison;
    use Event\Sourcing;
    use Command\Handling;

    private Encoder $encoder;
    private Clock $clock;

    public function __construct(User\Id $id, Encoder $encoder, Clock $clock)
    {
        $this->identifyBy($id);

        $this->encoder = $encoder;
        $this->clock = $clock;
    }

    public function userId() : User\Id
    {
        return $this->aggregateRootId();
    }

    /**
     * @see User::applyUserRegistered()
     */
    public function handleRegisterUser(Commands\RegisterUser $command) : void
    {
        Assert::email($command->email(), 'Invalid email given.');

        $hash = $this->encoder->encode($command->password());

        $this->apply(new Events\UserRegistered($this->userId()->toString(), $command->email(), $hash, $this->clock->now()));
    }

    /**
     * @see User::handleRegisterUser()
     */
    private function applyUserRegistered(Events\UserRegistered $event) : void
    {
    }
}
