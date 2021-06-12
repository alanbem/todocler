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
use Streak\Domain\AggregateRoot;
use Streak\Domain\Command;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Command\CreateTaskTest
 */
final class CreateTask implements Command\AggregateRootCommand
{
    public function __construct(private string $listId, private string $taskId, private string $name, private string $creatorId)
    {
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
