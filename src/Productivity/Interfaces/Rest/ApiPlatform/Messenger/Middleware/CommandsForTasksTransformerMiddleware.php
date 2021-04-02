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

namespace Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware;

use ApiPlatform\Core\Bridge\Symfony\Messenger\ContextStamp;
use Productivity\Application\Command as Command;
use Productivity\Application\Projector\Lists\Doctrine\Entity\Task;
use Productivity\Interfaces\Rest\ApiPlatform\DTO as DTOs;
use Productivity\UsersFacade;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This middleware transforms DTOs into commands.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\CommandsForTasksTransformerMiddlewareTest
 */
class CommandsForTasksTransformerMiddleware implements MiddlewareInterface
{
    private Security $security;
    private UsersFacade $users;

    public function __construct(Security $security, UsersFacade $users)
    {
        $this->security = $security;
        $this->users = $users;
    }

    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $authenticatedUserId = $this->authenticatedUserId();

        if (null === $authenticatedUserId) {
            return $stack->next()->handle($envelope, $stack);
        }

        $message = $envelope->getMessage();

        if ($message instanceof DTOs\CreateTask) {
            $command = $this->transformCreateTaskDTO($message, $authenticatedUserId);
            $envelope = Envelope::wrap($command);
        }

        if ($message instanceof DTOs\CompleteTask) {
            $task = $this->task($envelope);
            if (null === $task) {
                return $stack->next()->handle($envelope, $stack);
            }

            $command = $this->transformCompleteTaskDTO($message, $task->getList()->getId(), $task->getId(), $authenticatedUserId);
            $envelope = Envelope::wrap($command);
        }

        return $stack->next()->handle($envelope, $stack);
    }

    private function transformCompleteTaskDTO(DTOs\CompleteTask $dto, string $listId, string $taskId, string $registeredUserId) : Command\CompleteTask
    {
        $command = new Command\CompleteTask(
            $listId,
            $taskId,
            $registeredUserId,
        );

        return $command;
    }

    private function transformCreateTaskDTO(DTOs\CreateTask $dto, string $registeredUserId) : Command\CreateTask
    {
        $command = new Command\CreateTask(
            $dto->listId,
            $dto->taskId,
            $dto->name,
            $registeredUserId,
        );

        return $command;
    }

    public function task(Envelope $envelope) : ?Task
    {
        $stamps = $envelope->all(ContextStamp::class);

        if ([] === $stamps) {
            return null;
        }

        /** @var ContextStamp $stamp */
        $stamp = $stamps[0];
        $context = $stamp->getContext();

        if (false === isset($context['previous_data'])) {
            return null;
        }

        $resource = $context['previous_data'];

        if (!$resource instanceof Task) {
            return null;
        }

        return $resource;
    }

    private function authenticatedUserId() : ?string
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

        $user = $this->users->findRegisteredUser($user->getUsername());

        if (null === $user) {
            return null;
        }

        return $user->id;
    }
}
