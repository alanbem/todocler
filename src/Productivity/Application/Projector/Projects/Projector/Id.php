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

namespace Productivity\Application\Projector\Projects\Projector;

use Streak\Domain\Event\Listener;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Projector\Projects\Projector\IdTest
 */
final class Id implements Listener\Id
{
    private const ID = '00000000-0000-0000-0000-000000000000';

    public function equals($id) : bool
    {
        if (!$id instanceof self) {
            return false;
        }

        return true;
    }

    public function toString() : string
    {
        return self::ID;
    }

    public static function fromString(string $id) : self
    {
        return new self();
    }
}
