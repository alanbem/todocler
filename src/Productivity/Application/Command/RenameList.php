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
 * @see \Productivity\Application\Command\RenameListTest
 */
class RenameList implements Command\AggregateRootCommand
{
    private string $listId;
    private string $name;
    private string $editorId;

    public function __construct(string $listId, string $name, string $editorId)
    {
        $this->listId = $listId;
        $this->name = $name;
        $this->editorId = $editorId;
    }

    public function listId() : string
    {
        return $this->listId;
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
        return new Checklist\Id($this->listId);
    }
}
