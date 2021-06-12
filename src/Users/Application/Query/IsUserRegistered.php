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

namespace Users\Application\Query;

use Streak\Domain\Event\Listener;
use Streak\Domain\Query;
use Users\Application\Projector\RegisteredUsers;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Query\IsUserRegisteredTest
 */
final class IsUserRegistered implements Query\EventListenerQuery
{
    public function __construct(private string $email)
    {
    }

    public function email() : string
    {
        return $this->email;
    }

    public function listenerId() : Listener\Id
    {
        return new RegisteredUsers\Projector\Id();
    }
}
