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
 * @see \Productivity\Domain\Exception\ProjectNotFoundTest
 */
final class ProjectNotFound extends \RuntimeException
{
    public function __construct(private string $projectId)
    {
        $message = sprintf('Project "%s" not found.', $this->projectId);

        parent::__construct($message);
    }

    public function projectId() : string
    {
        return $this->projectId;
    }
}
