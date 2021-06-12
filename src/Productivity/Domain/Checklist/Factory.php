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

namespace Productivity\Domain\Checklist;

use Productivity\Domain\Checklist;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Clock;
use Streak\Domain\Exception\InvalidAggregateIdGiven;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Checklist\FactoryTest
 */
final class Factory implements AggregateRoot\Factory
{
    public function __construct(private Clock $clock)
    {
    }

    public function create(AggregateRoot\Id $id) : AggregateRoot
    {
        if (!$id instanceof Id) {
            throw new InvalidAggregateIdGiven($id);
        }

        return new Checklist($id, $this->clock);
    }
}
