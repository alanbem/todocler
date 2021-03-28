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

namespace Productivity\Domain\Checklist;

use Streak\Domain\AggregateRoot;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Checklist\IdTest
 */
class Id implements AggregateRoot\Id
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function equals(object $id) : bool
    {
        if (!$id instanceof self) {
            return false;
        }

        return $this->value === $id->value;
    }

    public function toString() : string
    {
        return $this->value;
    }

    public static function fromString(string $id) : self
    {
        return new static($id);
    }
}
