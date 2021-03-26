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

namespace Users\Domain\Event;

use Streak\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class UserRegistered implements Domain\Event
{
    private string $userId;
    private string $email;
    private string $passwordHash;
    private \DateTimeImmutable $registeredAt;

    public function __construct(string $userId, string $email, string $passwordHash, \DateTimeImmutable $registeredAt)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->registeredAt = $registeredAt;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function passwordHash() : string
    {
        return $this->passwordHash;
    }

    public function registeredAt() : \DateTimeImmutable
    {
        return $this->registeredAt;
    }
}
