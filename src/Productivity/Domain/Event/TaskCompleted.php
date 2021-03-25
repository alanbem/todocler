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
 */
class TaskCompleted implements Domain\Event
{
    private string $listId;
    private string $taskId;
    private string $userId;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $listId, string $taskId, string $userId, \DateTimeImmutable $createdAt)
    {
        $this->listId = $listId;
        $this->taskId = $taskId;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function taskId() : string
    {
        return $this->taskId;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function createdAt() : \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
