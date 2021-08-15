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

use Productivity\Domain\Project;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Command;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Command\RenameProjectTest
 */
final class RenameProject implements Command\AggregateRootCommand
{
    public function __construct(private string $projectId, private string $name, private string $editorId)
    {
    }

    public function projectId() : string
    {
        return $this->projectId;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function editorId() : string
    {
        return $this->editorId;
    }

    public function aggregateRootId() : AggregateRoot\Id
    {
        return new Project\Id($this->projectId);
    }
}
