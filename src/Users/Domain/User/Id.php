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

namespace Users\Domain\User;

use Streak\Domain\AggregateRoot;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Domain\User\IdTest
 */
final class Id implements AggregateRoot\Id
{
    public function __construct(private string $value)
    {
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
