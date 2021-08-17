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
 * @see \Productivity\Infrastructure\Interfaces\Console\Symfony\CreateProjectCommandTest
 */
final class CreateProjectCommand extends Command
{
    public function __construct(private CommandBus $bus, private UsersFacade $users)
    {
        parent::__construct('todocler:productivity:create-project');
    }

    protected function configure() : void
    {
        $this->setDescription('Create project.');
        $this->setDefinition(new InputDefinition([
            new InputArgument('project-id', InputArgument::REQUIRED, 'Define an id of a project.'),
            new InputArgument('name', InputArgument::REQUIRED, 'Define a name of a project.'),
            new InputArgument('email', InputArgument::REQUIRED, 'Define an email of a user owning this project.'),
        ]));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('project-id');
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');

        Assert::uuid($projectId, 'Project id must be an UUID.');
        Assert::notEmpty($name, 'Invalid project name given.');
        Assert::email($email, 'Invalid email given.');

        $user = $this->users->findRegisteredUser($email);

        Assert::notNull($user, 'User with given email not found.');

        $this->bus->dispatch(new Commands\CreateProject($projectId, $name, $user->id));

        $output->writeln(sprintf('<info>Project "%s" created successfully for "%s".</info>', $name, $email));

        return 0;
    }
}
