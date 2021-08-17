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

use Productivity\Application\Projector\Projects\Doctrine\Entity\Project;
use Productivity\Domain\Command;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\DTO;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Transforms DTO message into application command.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RenameProjectTransformingMiddlewareTest
 */
final class RenameProjectTransformingMiddleware extends TransformingMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $dto = $envelope->getMessage();

        if (!$dto instanceof DTO\RenameProject) {
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

        $command = new Command\RenameProject(
            $resource->getId(),
            $dto->name,
            $user->id,
        );

        // repack
        $envelope = Envelope::wrap($command);

        return $stack->next()->handle($envelope, $stack);
    }

    protected function resource(Envelope $envelope) : ?Project
    {
        $resource = parent::resource($envelope);

        if (!$resource instanceof Project) {
            return null;
        }

        return $resource;
    }
}
