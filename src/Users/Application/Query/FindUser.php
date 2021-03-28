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

use Streak\Application\Query;
use Streak\Domain\Event\Listener;
use Users\Application\Projector\RegisteredUsers;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Application\Query\FindUserTest
 */
class FindUser implements Query\EventListenerQuery
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
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
