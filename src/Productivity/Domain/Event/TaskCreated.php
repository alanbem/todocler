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
 * @see \Productivity\Domain\Event\TaskCreatedTest
 */
final class TaskCreated implements Domain\Event
{
    private const DATE_FORMAT = 'Y-m-d H:i:s.u P'; // microsecond precision
    private string $createdAt;

    public function __construct(private string $projectId, private string $taskId, private string $name, private string $creatorId, \DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt->format(self::DATE_FORMAT);
    }

    public function projectId() : string
    {
        return $this->projectId;
    }

    public function taskId() : string
    {
        return $this->taskId;
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
