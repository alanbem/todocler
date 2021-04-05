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

use PHPUnit\Framework\TestCase;
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
 * @covers \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RegisteredUserMiddleware
 */
final class RegisteredUserMiddlewareTest extends TestCase
{
    private Security $security;
    private UserInterface $user;
    private UsersFacade $facade;
    private StackInterface $stack;
    private MiddlewareInterface $next;

    protected function setUp() : void
    {
        $this->security = $this->createMock(Security::class);
        $this->user = $this->createMock(UserInterface::class);
        $this->facade = $this->createMock(UsersFacade::class);
        $this->stack = $this->createMock(StackInterface::class);
        $this->next = $this->createMock(MiddlewareInterface::class);

        $this->stack
            ->expects(self::once())
            ->method('next')
            ->willReturn($this->next);

        $this->next
            ->expects(self::once())
            ->method('handle')
            ->with(self::isInstanceOf(Envelope::class), $this->stack)
            ->willReturnCallback(fn (Envelope $envelope, StackInterface $stack) => $envelope);
    }

    public function testMiddleware() : void
    {
        $middleware = new RegisteredUserMiddleware($this->security, $this->facade);
        $envelope = Envelope::wrap(new \stdClass());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects(self::once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $user = (object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com'];
        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn($user);

        $expected = $envelope->with(new RegisteredUserStamp($user));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertEquals($expected, $actual);
    }

    public function testMiddlewareWhenUserNotAuthenticated() : void
    {
        $middleware = new RegisteredUserMiddleware($this->security, $this->facade);
        $envelope = Envelope::wrap(new \stdClass());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $this->user
            ->expects(self::never())
            ->method(self::anything());

        $this->facade
            ->expects(self::never())
            ->method(self::anything());

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWhenUserNotRegistered() : void
    {
        $middleware = new RegisteredUserMiddleware($this->security, $this->facade);
        $envelope = Envelope::wrap(new \stdClass());

        $this->security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects(self::once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects(self::once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn(null);

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }
}
