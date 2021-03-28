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

namespace Productivity\Application\Projector\Lists\Doctrine\Entity;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Task
{
    private Checklist $list;
    private string $id;
    private string $name;
    private string $userId;
    private bool $completed = false;
    private ?\DateTimeImmutable $updatedAt;
    private \DateTimeImmutable $createdAt;

    public function __construct(Checklist $list, string $id, string $name, string $userId, \DateTimeImmutable $updatedAt, \DateTimeImmutable $createdAt)
    {
        $this->list = $list;
        $this->id = $id;
        $this->name = $name;
        $this->userId = $userId;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }

    public function list() : Checklist
    {
        return $this->list;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function completed() : bool
    {
        return $this->completed;
    }

    public function complete(\DateTimeImmutable $when) : void
    {
        $this->completed = true;
        $this->updatedAt = $when;
    }

    public function updatedAt() : \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function createdAt() : \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
