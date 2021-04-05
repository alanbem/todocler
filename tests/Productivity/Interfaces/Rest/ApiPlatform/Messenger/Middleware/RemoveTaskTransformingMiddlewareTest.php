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

use ApiPlatform\Core\Bridge\Symfony\Messenger\RemoveStamp;
use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Lists\Doctrine\Entity as Entities;
use Productivity\Domain\Command as Commands;
use Productivity\Interfaces\Rest\ApiPlatform\Messenger\Stamp\RegisteredUserStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RemoveTaskTransformingMiddleware
 * @covers \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\TransformingMiddleware
 */
final class RemoveTaskTransformingMiddlewareTest extends TestCase
{
    private StackInterface $stack;
    private MiddlewareInterface $next;

    protected function setUp() : void
    {
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
        $middleware = new RemoveTaskTransformingMiddleware();

        $message = new Entities\Task(new Entities\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable()), '8b89a300-a95b-40af-b235-87ddf9e47309', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable());

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new RemoveStamp());

        $command = new Commands\RemoveTask('62fafb74-f550-4780-a527-37cb0b1e08ae', '8b89a300-a95b-40af-b235-87ddf9e47309', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertEquals($expected, $actual);
    }

    public function testMiddlewareWithWrongMessage() : void
    {
        $middleware = new RemoveTaskTransformingMiddleware();

        $message = new Entities\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable());

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new RemoveStamp());

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithoutStamp() : void
    {
        $middleware = new RemoveTaskTransformingMiddleware();

        $message = new Entities\Task(new Entities\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable()), '8b89a300-a95b-40af-b235-87ddf9e47309', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable());

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithoutRegisteredUser() : void
    {
        $middleware = new RemoveTaskTransformingMiddleware();

        $message = new Entities\Task(new Entities\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable()), '8b89a300-a95b-40af-b235-87ddf9e47309', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable());

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RemoveStamp());

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }
}
