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

use Productivity\Interfaces\Rest\ApiPlatform\Messenger\Stamp\RegisteredUserStamp;
use Productivity\UsersFacade;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RegisteredUserMiddlewareTest
 */
final class RegisteredUserMiddleware implements MiddlewareInterface
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
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            return $stack->next()->handle($envelope, $stack);
        }

        $user = $this->users->findRegisteredUser($user->getUsername());

        if (null === $user) {
            return $stack->next()->handle($envelope, $stack);
        }

        $envelope = $envelope->with(new RegisteredUserStamp($user));

        return $stack->next()->handle($envelope, $stack);
    }
}
