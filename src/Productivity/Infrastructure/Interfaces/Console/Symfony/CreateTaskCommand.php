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

namespace Productivity\Infrastructure\Interfaces\Console\Symfony;

use Productivity\Domain\Command\CreateTask;
use Productivity\UsersFacade;
use Streak\Application\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Infrastructure\Interfaces\Console\Symfony\CreateTaskCommandTest
 */
final class CreateTaskCommand extends Command
{
    public function __construct(private CommandBus $bus, private UsersFacade $users)
    {
        parent::__construct('todocler:productivity:create-task');
    }

    protected function configure() : void
    {
        $this->setDescription('Create task.');
        $this->setDefinition(new InputDefinition([
            new InputArgument('list-id', InputArgument::REQUIRED, 'Define an id of a list that this task belongs to.'),
            new InputArgument('task-id', InputArgument::REQUIRED, 'Define an id of a task.'),
            new InputArgument('name', InputArgument::REQUIRED, 'Define a name of a task.'),
            new InputArgument('email', InputArgument::REQUIRED, 'Define an email of a user owning this task.'),
        ]));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listId = $input->getArgument('list-id');
        $taskId = $input->getArgument('task-id');
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');

        Assert::uuid($listId, 'List id must be an UUID.');
        Assert::uuid($taskId, 'Task id must be an UUID.');
        Assert::notEmpty($name, 'Invalid list name given.');
        Assert::email($email, 'Invalid email given.');

        $user = $this->users->findRegisteredUser($email);

        Assert::notNull($user, 'User with given email not found.');

        $this->bus->dispatch(new CreateTask($listId, $taskId, $name, $user->id));

        $output->writeln(sprintf('<info>Task "%s" created successfully for "%s".</info>', $name, $email));

        return 0;
    }
}
