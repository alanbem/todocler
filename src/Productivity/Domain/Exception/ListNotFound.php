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
 * @see \Productivity\Domain\Exception\ListNotFoundTest
 */
class ListNotFound extends \RuntimeException
{
    private string $listId;

    public function __construct(string $listId)
    {
        $this->listId = $listId;

        $message = sprintf('List "%s" not found.', $this->listId);

        parent::__construct($message);
    }

    public function listId() : string
    {
        return $this->listId;
    }
}
