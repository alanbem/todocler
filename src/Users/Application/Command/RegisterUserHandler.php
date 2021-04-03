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

namespace Users\Application\Command;

use Streak\Application\Command;
use Streak\Application\CommandHandler;
use Streak\Domain\AggregateRoot;
use Streak\Domain\Exception;
use Users\Domain\Command\RegisterUser;
use Users\Domain\User;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
final class RegisterUserHandler implements CommandHandler
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
    public function handleRegisterUser(RegisterUser $command) : void
    {
        $userId = new User\Id($command->userId());

        $user = $this->repository->find($userId);

        if ($user) {
            throw new Exception\AggregateAlreadyExists($user);
        }

        /** @var User $user */
        $user = $this->factory->create($userId);

        $this->repository->add($user);

        $user->handle($command); // this aggregate is command handler, so we can pass $command directly
    }
}
