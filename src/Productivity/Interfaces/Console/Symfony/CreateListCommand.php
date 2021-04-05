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

namespace Productivity\Interfaces\Console\Symfony;

use Productivity\Domain\Command as Commands;
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
 * @see \Productivity\Interfaces\Console\Symfony\CreateListCommandTest
 */
final class CreateListCommand extends Command
{
    private CommandBus $bus;
    private UsersFacade $users;

    public function __construct(CommandBus $bus, UsersFacade $users)
    {
        $this->bus = $bus;
        $this->users = $users;

        parent::__construct('todocler:productivity:create-list');
    }

    protected function configure()
    {
        $this->setDescription('Create list.');
        $this->setDefinition(new InputDefinition([
            new InputArgument('list-id', InputArgument::REQUIRED, 'Define an id of a list.'),
            new InputArgument('name', InputArgument::REQUIRED, 'Define a name of a list.'),
            new InputArgument('email', InputArgument::REQUIRED, 'Define an email of a user owning this list.'),
        ]));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listId = $input->getArgument('list-id');
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');

        Assert::uuid($listId, 'List id must be an UUID.');
        Assert::notEmpty($name, 'Invalid list name given.');
        Assert::email($email, 'Invalid email given.');

        $user = $this->users->findRegisteredUser($email);

        Assert::notNull($user, 'User with given email not found.');

        $this->bus->dispatch(new Commands\CreateList($listId, $name, $user->id));

        $output->writeln(sprintf('<info>List "%s" created successfully for "%s".</info>', $name, $email));

        return 0;
    }
}
