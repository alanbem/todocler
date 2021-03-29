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

use Productivity;
use Streak\Application\QueryBus;
use Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUser;
use Users\Application\Query\FindUser;
use Users\Application\Query\IsUserRegistered;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Infrastructure\UsersFacadeForProductivityTest
 */
class UsersFacadeForProductivity implements Productivity\UsersFacade
{
    private QueryBus $bus;

    public function __construct(QueryBus $bus)
    {
        $this->bus = $bus;
    }

    public function isUserRegistered(string $email) : bool
    {
        return $this->bus->dispatch(new IsUserRegistered($email));
    }

    public function findRegisteredUser($email) : ?object
    {
        /** @var ?RegisteredUser $user */
        $user = $this->bus->dispatch(new FindUser($email));

        if (null === $user) {
            return null;
        }

        return (object) [
            'id' => $user->getId(),
            'email' => $user->getUsername(),
        ];
    }
}
