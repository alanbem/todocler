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
 * @see \Productivity\Domain\Exception\TaskNotFoundTest
 */
final class TaskNotFound extends \RuntimeException
{
    public function __construct(private string $projectId, private string $taskId)
    {
        $message = sprintf('Task "%s" not found.', $this->taskId);

        parent::__construct($message);
    }

    public function projectId() : string
    {
        return $this->projectId;
    }

    public function taskId() : string
    {
        return $this->taskId;
    }
}
