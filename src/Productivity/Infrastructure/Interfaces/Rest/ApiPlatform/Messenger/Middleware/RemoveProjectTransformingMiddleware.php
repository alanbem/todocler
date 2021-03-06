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

use ApiPlatform\Core\Bridge\Symfony\Messenger\RemoveStamp;
use Productivity\Application\Projector\Projects\Doctrine\Entity;
use Productivity\Domain\Command;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Transforms remove request into application command.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RemoveProjectTransformingMiddlewareTest
 */
final class RemoveProjectTransformingMiddleware extends TransformingMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        $resource = $envelope->getMessage();

        // input option does not work with DELETE method - resource object with proper stamp are used.
        if (!$resource instanceof Entity\Project) {
            return $stack->next()->handle($envelope, $stack);
        }

        // this is how api platform inform about DELETE of resource
        if (null === $envelope->last(RemoveStamp::class)) {
            return $stack->next()->handle($envelope, $stack);
        }

        $user = $this->user($envelope);

        if (null === $user) {
            return $stack->next()->handle($envelope, $stack);
        }

        $command = new Command\RemoveProject(
            $resource->getId(),
            $user->id,
        );

        // repack
        $envelope = Envelope::wrap($command);

        return $stack->next()->handle($envelope, $stack);
    }
}
