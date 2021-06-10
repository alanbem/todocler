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
 * @see \Productivity\Domain\Command\RemoveTaskTest
 */
final class RemoveTask implements Command\AggregateRootCommand
{
    private string $taskId;
    private string $listId;
    private string $removerId;

    public function __construct(string $listId, string $taskId, string $removerId)
    {
        $this->listId = $listId;
        $this->taskId = $taskId;
        $this->removerId = $removerId;
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function taskId() : string
    {
        return $this->taskId;
    }

    public function removerId() : string
    {
        return $this->removerId;
    }

    public function aggregateRootId() : AggregateRoot\Id
    {
        return new Checklist\Id($this->listId);
    }
}
