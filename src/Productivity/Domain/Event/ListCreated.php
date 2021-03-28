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

namespace Productivity\Domain\Event;

use Streak\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Event\ListCreatedTest
 */
final class ListCreated implements Domain\Event
{
    const DATE_FORMAT = 'U.u'; // microsecond precision

    private string $listId;
    private string $name;
    private string $creatorId;
    private string $createdAt;

    public function __construct(string $listId, string $name, string $creatorId, \DateTimeImmutable $createdAt)
    {
        $this->listId = $listId;
        $this->name = $name;
        $this->creatorId = $creatorId;
        $this->createdAt = $createdAt->format(self::DATE_FORMAT);
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function creatorId() : string
    {
        return $this->creatorId;
    }

    public function createdAt() : \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->createdAt);
    }
}
