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

use Productivity\Domain\Project\Task;
use Streak\Domain\Event;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Event\TaskCompletedTest
 */
final class TaskCompleted implements Event, Event\EntityEvent
{
    private const DATE_FORMAT = 'Y-m-d H:i:s.u P'; // microsecond precision
    private string $completedAt;

    public function __construct(private string $projectId, private string $taskId, private string $userId, \DateTimeImmutable $completedAt)
    {
        $this->completedAt = $completedAt->format(self::DATE_FORMAT);
    }

    public function projectId() : string
    {
        return $this->projectId;
    }

    public function taskId() : string
    {
        return $this->taskId;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function completedAt() : \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->completedAt);
    }

    public function entityId() : Task\Id
    {
        return new Task\Id($this->taskId);
    }
}
