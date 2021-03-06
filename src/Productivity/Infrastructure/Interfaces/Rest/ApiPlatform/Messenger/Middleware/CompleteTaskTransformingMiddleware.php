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

use Productivity\Application\Projector\Projects\Doctrine\Entity;
use Productivity\Domain\Command;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\DTO;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Transforms DTO message into application command.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\CompleteTaskTransformingMiddlewareTest
 */
final class CompleteTaskTransformingMiddleware extends TransformingMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $dto = $envelope->getMessage();

        if (!$dto instanceof DTO\CompleteTask) {
            return $stack->next()->handle($envelope, $stack);
        }

        $resource = $this->resource($envelope);

        if (null === $resource) {
            return $stack->next()->handle($envelope, $stack);
        }

        $user = $this->user($envelope);

        if (null === $user) {
            return $stack->next()->handle($envelope, $stack);
        }

        $command = new Command\CompleteTask(
            $resource->getProject()->getId(),
            $resource->getId(),
            $user->id,
        );

        // repack
        $envelope = Envelope::wrap($command);

        return $stack->next()->handle($envelope, $stack);
    }

    protected function resource(Envelope $envelope) : ?Entity\Task
    {
        $resource = parent::resource($envelope);

        if (!$resource instanceof Entity\Task) {
            return null;
        }

        return $resource;
    }
}
