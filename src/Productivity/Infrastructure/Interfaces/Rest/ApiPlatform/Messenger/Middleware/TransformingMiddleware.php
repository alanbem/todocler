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

use ApiPlatform\Core\Bridge\Symfony\Messenger\ContextStamp;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Stamp\RegisteredUserStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
abstract class TransformingMiddleware implements MiddlewareInterface
{
    protected function resource(Envelope $envelope) : ?object
    {
        /** @var ContextStamp $stamp */
        $stamp = $envelope->last(ContextStamp::class);

        if (null === $stamp) {
            return null;
        }

        $context = $stamp->getContext();

        if (false === isset($context['previous_data'])) {
            return null;
        }

        $resource = $context['previous_data'];

        if (false === \is_object($resource)) {
            return null;
        }

        return $resource;
    }

    protected function user(Envelope $envelope) : ?object
    {
        $stamp = $envelope->last(RegisteredUserStamp::class);

        if (!$stamp instanceof RegisteredUserStamp) {
            return null;
        }

        return $stamp->user();
    }
}
