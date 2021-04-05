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

namespace Productivity;

/**
 * Facade for `Users` bounded context. `Productivity` context should communicate with `Users` context through this facade.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface UsersFacade
{
    /**
     * Check whether user with given email exists.
     */
    public function isUserRegistered(string $email) : bool;

    /**
     * Finds user by its email.
     */
    public function findRegisteredUser($email) : ?object; // instead of object it should be typed DTO tailored for `Productivity` context
}
