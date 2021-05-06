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

use Streak\Domain\Command;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Command\CreateListTest
 */
final class CreateList implements Command
{
    private string $listId;
    private string $name;
    private string $creatorId;

    public function __construct(string $listId, string $name, string $creatorId)
    {
        $this->listId = $listId;
        $this->name = $name;
        $this->creatorId = $creatorId;
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function creatorId() : string
    {
        return $this->creatorId;
    }
}
