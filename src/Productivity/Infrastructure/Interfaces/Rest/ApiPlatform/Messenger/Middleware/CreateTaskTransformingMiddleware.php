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

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware;

use Productivity\Domain\Command;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\DTO;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Transforms DTO message into application command.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\CreateTaskTransformingMiddlewareTest
 */
final class CreateTaskTransformingMiddleware extends TransformingMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $dto = $envelope->getMessage();

        if (!$dto instanceof DTO\CreateTask) {
            return $stack->next()->handle($envelope, $stack);
        }

        $user = $this->user($envelope);

        if (null === $user) {
            return $stack->next()->handle($envelope, $stack);
        }

        $command = new Command\CreateTask(
            $dto->projectId,
            $dto->taskId,
            $dto->name,
            $user->id,
        );

        // repack
        $envelope = Envelope::wrap($command);

        return $stack->next()->handle($envelope, $stack);
    }
}
