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

namespace Productivity\Domain\Exception;

use Productivity\Domain\Checklist;
use Productivity\Domain\Checklist\Task;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class TaskAlreadyCompleted extends \RuntimeException
{
    private Checklist\Id $listId;
    private Task\Id $taskId;

    public function __construct(Checklist\Id $listId, Task\Id $taskId)
    {
        $this->listId = $listId;
        $this->taskId = $taskId;

        $message = sprintf('Task "%s" already completed.', $this->taskId->toString());

        parent::__construct($message);
    }

    public function listId() : Checklist\Id
    {
        return $this->listId;
    }

    public function taskId() : Task\Id
    {
        return $this->taskId;
    }
}
