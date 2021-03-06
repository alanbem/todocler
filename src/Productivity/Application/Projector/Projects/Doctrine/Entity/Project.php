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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Projector\Doctrine\Entity\ProjectTest
 *
 * @noRector \Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector
 */
class Project
{
    private string $id;
    private string $name;
    private string $userId;
    /**
     * @var Task[]
     */
    private iterable $tasks;
    private ?\DateTimeImmutable $updatedAt;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, string $name, string $userId, \DateTimeImmutable $updatedAt, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->userId = $userId;
        $this->tasks = new ArrayCollection();
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
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

    public function rename(string $name, \DateTimeImmutable $when) : void
    {
        $this->name = $name;
        $this->updatedAt = $when;
    }

    public function addTask(string $id, string $name, string $userId, \DateTimeImmutable $updatedAt, \DateTimeImmutable $createdAt) : void
    {
        $this->tasks->add(new Task($this, $id, $name, $userId, $updatedAt, $createdAt));
    }

    /**
     * @return Task[]
     */
    public function getTasks() : iterable
    {
        return $this->tasks->getValues();
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
