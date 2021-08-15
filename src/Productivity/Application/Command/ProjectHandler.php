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

use Productivity\Domain\Command\CreateProject;
use Productivity\Domain\Project;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Command;
use Streak\Domain\CommandHandler;
use Streak\Domain\Exception;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
final class ProjectHandler implements CommandHandler
{
    use Command\Handling;

    private AggregateRoot\Factory $factory;
    private AggregateRoot\Repository $repository;

    public function __construct(AggregateRoot\Factory $factory, AggregateRoot\Repository $repository)
    {
        $this->factory = $factory;
        $this->repository = $repository;
    }

    /**
     * @see Command\Handling::handle()
     */
    public function handleCreateList(CreateProject $command) : void
    {
        $projectId = new Project\Id($command->projectId());

        $project = $this->repository->find($projectId);

        if ($project) {
            throw new Exception\AggregateAlreadyExists($project);
        }

        /** @var Project $project */
        $project = $this->factory->create($projectId);

        $this->repository->add($project);

        $project->handleCommand($command); // this aggregate is command handler, so we can pass $command directly
    }
}
