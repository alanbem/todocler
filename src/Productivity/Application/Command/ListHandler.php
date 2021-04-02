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

use Productivity\Domain\Checklist;
use Streak\Application\Command;
use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Exception;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class ListHandler implements CommandHandler
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
    public function handleCreateList(CreateList $command) : void
    {
        $listId = new Checklist\Id($command->listId());

        $list = $this->repository->find($listId);

        if ($list) {
            throw new Exception\AggregateAlreadyExists($list);
        }

        /** @var Checklist $list */
        $list = $this->factory->create($listId);

        $this->repository->add($list);

        $list->handle($command); // this aggregate is command handler, so we can pass $command directly
    }
}
