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

namespace Users\Interfaces\Console\Symfony;

use Streak\Application\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Users\Application\Command\RegisterUser;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Interfaces\Console\Symfony\RegisterUserCommandTest
 */
class RegisterUserCommand extends Command
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;

        parent::__construct('todocler:users:register-user');
    }

    protected function configure()
    {
        $this->setDescription('Register new user.');
        $this->setDefinition(new InputDefinition([
            new InputArgument('id', InputArgument::REQUIRED, 'Define an id of a user to be created.'),
            new InputArgument('email', InputArgument::REQUIRED, 'Define an email of a user to be created.'),
            new InputArgument('password', InputArgument::REQUIRED, 'Define a password of a user to be created.'),
        ]));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('id');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        Assert::uuid($userId, 'User id must be an UUID.');
        Assert::email($email, 'Invalid email given.');
        Assert::notEmpty($password, 'Invalid password given.');

        $this->bus->dispatch(new RegisterUser($userId, $email, $password));

        $output->writeln(sprintf('User "%s" registered successfully.', $email));

        return 0;
    }
}
