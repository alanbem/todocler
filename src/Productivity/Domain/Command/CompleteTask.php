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

namespace Productivity\Domain\Command;

use Productivity\Domain\Checklist;
use Streak\Application\Command;
use Streak\Domain\AggregateRoot;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Command\CompleteTaskTest
 */
final class CompleteTask implements Command\AggregateRootCommand
{
    private string $listId;
    private string $taskId;
    private string $userId;

    public function __construct(string $listId, string $taskId, string $creatorId)
    {
        $this->listId = $listId;
        $this->taskId = $taskId;
        $this->userId = $creatorId;
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

    public function aggregateRootId() : AggregateRoot\Id
    {
        return new Checklist\Id($this->listId);
    }
}
