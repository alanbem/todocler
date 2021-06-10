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

use Streak\Domain\Entity;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
final class Task implements Entity
{
    use Entity\Comparison;
    use Entity\Identification;

    private string $name;
    private \DateTimeImmutable $createdAt;
    private bool $completed = false;

    public function __construct(Task\Id $id, string $name, \DateTimeImmutable $createdAt)
    {
        $this->identifyBy($id);

        $this->name = $name;
        $this->createdAt = $createdAt;
    }

    public function completed() : bool
    {
        return $this->completed;
    }

    public function complete() : void
    {
        $this->completed = true;
    }
}
