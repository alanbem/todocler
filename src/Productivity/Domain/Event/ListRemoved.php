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

namespace Productivity\Domain\Event;

use Streak\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Domain\Event\ListRemovedTest
 */
final class ListRemoved implements Domain\Event
{
    private const DATE_FORMAT = 'Y-m-d H:i:s.u P'; // microsecond precision

    private string $listId;
    private string $removerId;
    private string $removedAt;

    public function __construct(string $listId, string $removerId, \DateTimeImmutable $modifiedAt)
    {
        $this->listId = $listId;
        $this->removerId = $removerId;
        $this->removedAt = $modifiedAt->format(self::DATE_FORMAT);
    }

    public function listId() : string
    {
        return $this->listId;
    }

    public function removerId() : string
    {
        return $this->removerId;
    }

    public function removedAt() : \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->removedAt);
    }
}
