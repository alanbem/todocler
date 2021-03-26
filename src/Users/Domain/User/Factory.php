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

namespace Users\Domain\User;

use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Exception\InvalidAggregateIdGiven;
use Users\Domain\Encoder;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Factory implements AggregateRoot\Factory
{
    private Encoder $encoder;
    private Clock $clock;

    public function __construct(Encoder $encoder, Clock $clock)
    {
        $this->encoder = $encoder;
        $this->clock = $clock;
    }

    public function create(AggregateRoot\Id $id) : AggregateRoot
    {
        if (!$id instanceof User\Id) {
            throw new InvalidAggregateIdGiven($id);
        }

        return new User($id, $this->encoder, $this->clock);
    }
}
