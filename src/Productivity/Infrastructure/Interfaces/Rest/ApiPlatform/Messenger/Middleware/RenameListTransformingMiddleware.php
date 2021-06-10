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

use Productivity\Application\Projector\Lists\Doctrine\Entity\Checklist;
use Productivity\Domain\Command;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\DTO;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Transforms DTO message into application command.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RenameListTransformingMiddlewareTest
 */
final class RenameListTransformingMiddleware extends TransformingMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $dto = $envelope->getMessage();

        if (!$dto instanceof DTO\RenameList) {
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

        $command = new Command\RenameList(
            $resource->getId(),
            $dto->name,
            $user->id,
        );

        // repack
        $envelope = Envelope::wrap($command);

        return $stack->next()->handle($envelope, $stack);
    }

    protected function resource(Envelope $envelope) : ?Checklist
    {
        $resource = parent::resource($envelope);

        if (!$resource instanceof Checklist) {
            return null;
        }

        return $resource;
    }
}
