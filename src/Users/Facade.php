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

namespace Users;

/**
 * Facade for `Users` boundary context. All other contexts should communicate with this one through this facade.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface Facade
{
    /**
     * Check whether user with given username/email exists.
     */
    public function isUserRegistered(string $username) : bool;
}
