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

use Streak\Application\Command;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class CreateList implements Command
{
    private string $listId;
    private string $creatorId;

    public function __construct(string $listId, string $creatorId)
    {
        $this->listId = $listId;
        $this->creatorId = $creatorId;
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function creatorId() : string
    {
        return $this->creatorId;
    }
}
