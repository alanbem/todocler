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

namespace Productivity\Application\Event;

use Streak\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Event\UserRegisteredTest
 */
final class UserRegistered implements Domain\Event
{
    private const DATE_FORMAT = 'Y-m-d H:i:s.u P'; // microsecond precision
    private string $registeredAt;

    public function __construct(private string $userId, private string $email, \DateTimeImmutable $registeredAt)
    {
        $this->registeredAt = $registeredAt->format(self::DATE_FORMAT);
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function registeredAt() : \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->registeredAt);
    }
}
