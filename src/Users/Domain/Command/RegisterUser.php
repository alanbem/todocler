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

namespace Users\Domain\Command;

use Streak\Domain\Command;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Domain\Command\RegisterUserTest
 */
final class RegisterUser implements Command
{
    private string $userId;
    private string $email;
    private string $password;

    public function __construct(string $userId, string $email, string $password)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->password = $password;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function password() : string
    {
        return $this->password;
    }
}
