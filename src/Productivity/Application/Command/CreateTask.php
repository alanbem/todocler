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

namespace Productivity\Application\Command;

use Productivity\Domain\Checklist;
use Streak\Application\Command;
use Streak\Domain\AggregateRoot;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Command\CreateTaskTest
 */
final class CreateTask implements Command\AggregateRootCommand
{
    private string $listId;
    private string $taskId;
    private string $name;
    private string $creatorId;

    public function __construct(string $listId, string $taskId, string $name, string $creatorId)
    {
        $this->listId = $listId;
        $this->taskId = $taskId;
        $this->name = $name;
        $this->creatorId = $creatorId;
    }

    public function listId() : string
    {
        return $this->listId;
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

    public function aggregateRootId() : AggregateRoot\Id
    {
        return new Checklist\Id($this->listId);
    }
}
