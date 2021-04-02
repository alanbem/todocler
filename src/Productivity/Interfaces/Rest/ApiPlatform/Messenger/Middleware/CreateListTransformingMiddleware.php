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

use Productivity\Application\Command;
use Productivity\Interfaces\Rest\ApiPlatform\DTO;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Transforms DTO message into application command.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\CreateListTransformingMiddlewareTest
 */
final class CreateListTransformingMiddleware extends TransformingMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $dto = $envelope->getMessage();

        if (!$dto instanceof DTO\CreateList) {
            return $stack->next()->handle($envelope, $stack);
        }

        $user = $this->user($envelope);

        if (null === $user) {
            return $stack->next()->handle($envelope, $stack);
        }

        $command = new Command\CreateList(
            $dto->listId,
            $dto->name,
            $user->id,
        );

        // repack
        $envelope = Envelope::wrap($command);

        return $stack->next()->handle($envelope, $stack);
    }
}