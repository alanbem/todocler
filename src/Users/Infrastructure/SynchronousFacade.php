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

namespace Users\Infrastructure;

use Streak\Application\QueryBus;
use Users\Application\Query\IsUserRegistered;
use Users\Facade;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Infrastructure\SynchronousFacadeTest
 */
class SynchronousFacade implements Facade
{
    private QueryBus $bus;

    public function __construct(QueryBus $bus)
    {
        $this->bus = $bus;
    }

    public function isUserRegistered(string $username) : bool
    {
        return $this->bus->dispatch(new IsUserRegistered($username));
    }
}
