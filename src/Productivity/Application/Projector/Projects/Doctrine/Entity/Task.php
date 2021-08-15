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

namespace Productivity\Application\Projector\Projects\Doctrine\Entity;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Projector\Doctrine\Entity\TaskTest
 *
 * @noRector \Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector
 */
class Task
{
    private Project $project;
    private string $id;
    private string $name;
    private string $userId;
    private bool $completed = false;
    private ?\DateTimeImmutable $updatedAt;
    private \DateTimeImmutable $createdAt;

    public function __construct(Project $project, string $id, string $name, string $userId, \DateTimeImmutable $updatedAt, \DateTimeImmutable $createdAt)
    {
        $this->project = $project;
        $this->id = $id;
        $this->name = $name;
        $this->userId = $userId;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }

    public function getProject() : Project
    {
        return $this->project;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUserId() : string
    {
        return $this->userId;
    }

    public function isCompleted() : bool
    {
        return $this->completed;
    }

    public function complete(\DateTimeImmutable $when) : void
    {
        $this->completed = true;
        $this->updatedAt = $when;
    }

    public function getUpdatedAt() : \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt() : \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
