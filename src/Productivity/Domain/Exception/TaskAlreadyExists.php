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

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Exception\TaskAlreadyExistsTest
 */
final class TaskAlreadyExists extends \RuntimeException
{
    private string $listId;
    private string $taskId;

    public function __construct(string $listId, string $taskId)
    {
        $this->listId = $listId;
        $this->taskId = $taskId;

        $message = sprintf('Task "%s" already exist.', $this->taskId);

        parent::__construct($message);
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function taskId() : string
    {
        return $this->taskId;
    }
}
